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
package net.sf.jasperreports.engine.fill;

import java.sql.Connection;
import java.util.Map;
import java.util.concurrent.Executor;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * Class used to perform report filling asychronously.
 * <p>
 * An instance of this type can be used as a handle to an asychronous fill process.
 * The main benefit of this method is that the filling process can be cancelled.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: AsynchronousFillHandle.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class AsynchronousFillHandle extends BaseFillHandle
{	
	
	/**
	 * A property that determines whether a report can be generated and displayed asynchronously in a viewer.
	 * 
	 * Asynchronous report generation implies displaying report pages before the report is complete.
	 */
	// TODO lucianc use in web viewer
	public static final String PROPERTY_REPORT_ASYNC = JRPropertiesUtil.PROPERTY_PREFIX + "viewer.async";
	
	protected Thread fillThread;
	protected Integer priority;
	protected String threadName;
	
	protected AsynchronousFillHandle (
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters,
		JRDataSource dataSource
		) throws JRException
	{
		this(jasperReportsContext, jasperReport, parameters, dataSource, null);
	}
	
	protected AsynchronousFillHandle (
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters,
		Connection conn
		) throws JRException
	{
		this(jasperReportsContext, jasperReport, parameters, null, conn);
	}
	
	protected AsynchronousFillHandle (
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters
		) throws JRException
	{
		this(jasperReportsContext, jasperReport, parameters, null, null);
	}
	
	protected AsynchronousFillHandle (
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters,
		JRDataSource dataSource,
		Connection conn
		) throws JRException
	{
		super(jasperReportsContext, jasperReport, parameters, dataSource, conn);
	}
	
	/**
	 * Returns an executor that creates a new thread to perform the report execution.
	 */
	@Override
	protected Executor getReportExecutor()
	{
		return new ThreadExecutor();
	}

	protected class ThreadExecutor implements Executor
	{
		public void execute(Runnable command)
		{
			if (threadName == null)
			{
				fillThread = new Thread(command);
			}
			else
			{
				fillThread = new Thread(command, threadName);
			}
			
			if (priority != null)
			{
				fillThread.setPriority(priority.intValue());
			}
			
			fillThread.start();
		}
	}

	/**
	 * Creates an asychronous filling handle.
	 * 
	 * @param jasperReportsContext the context
	 * @param jasperReport the report
	 * @param parameters the parameter map
	 * @param dataSource the data source
	 * @return the handle
	 * @throws JRException
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters,
		JRDataSource dataSource
		) throws JRException
	{
		return new AsynchronousFillHandle(jasperReportsContext, jasperReport, parameters, dataSource);
	}


	/**
	 * Creates an asychronous filling handle.
	 * 
	 * @param jasperReportsContext the context
	 * @param jasperReport the report
	 * @param parameters the parameter map
	 * @param conn the connection
	 * @return the handle
	 * @throws JRException
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters,
		Connection conn
		) throws JRException
	{
		return new AsynchronousFillHandle(jasperReportsContext, jasperReport, parameters, conn);
	}


	/**
	 * Creates an asychronous filling handle.
	 * 
	 * @param jasperReportsContext the context
	 * @param jasperReport the report
	 * @param parameters the parameter map
	 * @return the handle
	 * @throws JRException
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReportsContext jasperReportsContext,
		JasperReport jasperReport,
		Map<String,Object> parameters
		) throws JRException
	{
		return new AsynchronousFillHandle(jasperReportsContext, jasperReport, parameters);
	}
	
	
	/**
	 * @see #createHandle(JasperReportsContext, JasperReport, Map, JRDataSource)
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReport jasperReport,
		Map<String,Object> parameters,
		JRDataSource dataSource
		) throws JRException
	{
		return createHandle(DefaultJasperReportsContext.getInstance(), jasperReport, parameters, dataSource);
	}


	/**
	 * @see #createHandle(JasperReportsContext, JasperReport, Map, Connection)
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReport jasperReport,
		Map<String,Object> parameters,
		Connection conn
		) throws JRException
	{
		return createHandle(DefaultJasperReportsContext.getInstance(), jasperReport, parameters, conn);
	}


	/**
	 * @see #createHandle(JasperReportsContext, JasperReport, Map)
	 */
	public static AsynchronousFillHandle createHandle(
		JasperReport jasperReport,
		Map<String,Object> parameters
		) throws JRException
	{
		return createHandle(DefaultJasperReportsContext.getInstance(), jasperReport, parameters);
	}
	
	
	/**
	 * Sets the priority of the filler thread.
	 * 
	 * @param priority the filler thread priority.
	 * @see Thread#setPriority(int)
	 */
	public void setPriority (int priority)
	{
		synchronized (lock)
		{
			this.priority = Integer.valueOf(priority);
			if (fillThread != null)
			{
				fillThread.setPriority(priority);
			}
		}
	}
	
	
	/**
	 * Sets the name of the filler thread.
	 * 
	 * @param name the filler thread name.
	 * @see Thread#setName(java.lang.String)
	 */
	public void setThreadName (String name)
	{
		synchronized (lock)
		{
			this.threadName = name;
			if (fillThread != null)
			{
				fillThread.setName(name);
			}
		}
	}
}
