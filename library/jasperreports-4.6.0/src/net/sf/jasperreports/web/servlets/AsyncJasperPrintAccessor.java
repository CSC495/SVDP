/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.web.servlets;

import java.util.HashMap;
import java.util.Map;
import java.util.concurrent.locks.Condition;
import java.util.concurrent.locks.Lock;
import java.util.concurrent.locks.ReentrantLock;

import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.fill.AsynchronousFilllListener;
import net.sf.jasperreports.engine.fill.FillHandle;
import net.sf.jasperreports.engine.fill.FillListener;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * Generated report accessor used for asynchronous report executions that publishes pages
 * before the entire report has been generated.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: AsyncJasperPrintAccessor.java 5299 2012-04-25 14:49:49Z lucianc $
 */
public class AsyncJasperPrintAccessor implements JasperPrintAccessor, AsynchronousFilllListener, FillListener
{

	private static final Log log = LogFactory.getLog(AsyncJasperPrintAccessor.class);
	
	private final FillHandle fillHandle;
	private final Lock lock;
	private final Condition pageCondition;
	private final Map<Integer, Long> trackedPages = new HashMap<Integer, Long>();
	
	private volatile boolean done;
	private boolean cancelled;
	private Throwable error;
	private volatile JasperPrint jasperPrint;
	private int pageCount;
	
	/**
	 * Create a report accessor.
	 * 
	 * @param fillHandle the asynchronous fill handle used by this accessor
	 */
	public AsyncJasperPrintAccessor(FillHandle fillHandle)
	{
		this.fillHandle = fillHandle;
		lock = new ReentrantLock(true);
		pageCondition = lock.newCondition();
		
		fillHandle.addListener(this);
		fillHandle.addFillListener(this);
	}

	protected void lock()
	{
		try
		{
			lock.lockInterruptibly();
		}
		catch (InterruptedException e)
		{
			throw new JRRuntimeException("Interrupted while attempting to lock", e);
		}
	}

	protected void unlock()
	{
		lock.unlock();
	}
	
	public ReportPageStatus pageStatus(int pageIdx, Long pageTimestamp)
	{
		if (!done)
		{
			lock();
			try
			{
				// wait until the page is available
				while (!done && pageIdx >= pageCount)
				{
					if (log.isDebugEnabled())
					{
						log.debug("waiting for page " + pageIdx);
					}
					
					pageCondition.await();
				}
			}
			catch (InterruptedException e)
			{
				throw new JRRuntimeException(e);
			}
			finally
			{
				unlock();
			}
		}
		
		if (pageIdx >= pageCount)
		{
			return ReportPageStatus.NO_SUCH_PAGE;
		}
		
		if ((done && !cancelled && error == null) || fillHandle.isPageFinal(pageIdx))
		{
			trackedPages.remove(pageIdx);
			return ReportPageStatus.PAGE_FINAL;
		}
		
		long timestamp;
		boolean modified;
		
		Long lastUpdate = trackedPages.get(pageIdx);
		if (lastUpdate == null)
		{
			// we don't know when exactly the page was modified, using current time
			timestamp = System.currentTimeMillis();
			modified = true;
		}
		else
		{
			timestamp = lastUpdate;
			modified = pageTimestamp == null || pageTimestamp < lastUpdate;
		}
		
		ReportPageStatus status = ReportPageStatus.nonFinal(timestamp, modified);
		// add the page to the tracked map so that we catch updates
		trackedPages.put(pageIdx, timestamp);
		return status;
	}

	public JasperPrint getJasperPrint()
	{
		return jasperPrint;
	}

	public JasperPrint getFinalJasperPrint()
	{
		if (!done)
		{
			lock();
			try
			{
				// wait until the report generation is done
				while (!done)
				{
					if (log.isDebugEnabled())
					{
						log.debug("waiting for report end");
					}
					
					pageCondition.await();
				}
			}
			catch (InterruptedException e)
			{
				throw new JRRuntimeException(e);
			}
			finally
			{
				unlock();
			}
		}
		
		if (error != null)
		{
			throw new JRRuntimeException("Error occured during report generation", error);
		}
		
		if (jasperPrint == null)
		{
			throw new JRRuntimeException("No JasperPrint generated");
		}
		
		return jasperPrint;
	}

	public void reportFinished(JasperPrint jasperPrint)
	{
		if (log.isDebugEnabled())
		{
			log.debug("report finished");
		}
		
		lock();
		try
		{
			if (this.jasperPrint == null)
			{
				this.jasperPrint = jasperPrint;
			}
			
			pageCount = jasperPrint.getPages().size();
			done = true;
			trackedPages.clear();
			
			pageCondition.signalAll();
		}
		finally
		{
			unlock();
		}
	}

	public void reportCancelled()
	{
		if (log.isDebugEnabled())
		{
			log.debug("report cancelled");
		}
		
		lock();
		try
		{
			cancelled = true;
			done = true;
			pageCount = jasperPrint == null ? 0 : jasperPrint.getPages().size();

			// store an error as cancelled status
			error = new JRRuntimeException("Report generation cancelled");
			
			// signal to pageStatus
			pageCondition.signalAll();
		}
		finally
		{
			unlock();
		}
	}

	public void reportFillError(Throwable t)
	{
		log.error("Error during report execution", t);
		
		lock();
		try
		{
			error = t;
			done = true;
			pageCount = jasperPrint == null ? 0 : jasperPrint.getPages().size();
			
			// signal to pageStatus
			pageCondition.signalAll();
		}
		finally
		{
			unlock();
		}
	}

	public void pageGenerated(JasperPrint jasperPrint, int pageIndex)
	{
		if (log.isDebugEnabled())
		{
			log.debug("page " + pageIndex + " generated");
		}
		
		lock();
		try
		{
			if (this.jasperPrint == null)
			{
				this.jasperPrint = jasperPrint;
			}
			
			pageCount = pageIndex + 1;
			
			pageCondition.signalAll();
		}
		finally
		{
			unlock();
		}
	}

	public void pageUpdated(JasperPrint jasperPrint, int pageIndex)
	{
		if (log.isDebugEnabled())
		{
			log.debug("page " + pageIndex + " updated");
		}
		
		lock();
		try
		{
			// update the timestamp if the page is tracked
			if (trackedPages.containsKey(pageIndex))
			{
				long timestamp = System.currentTimeMillis();
				trackedPages.put(pageIndex, timestamp);
			}
		}
		finally
		{
			unlock();
		}
	}

	@Override
	public ReportExecutionStatus getReportStatus()
	{
		if (!done)
		{
			return ReportExecutionStatus.running(pageCount);
		}
		
		if (cancelled)
		{
			return ReportExecutionStatus.canceled(pageCount);
		}
		
		if (error != null)
		{
			return ReportExecutionStatus.error(pageCount, error);
		}
		
		return ReportExecutionStatus.finished(jasperPrint.getPages().size());
	}

}
