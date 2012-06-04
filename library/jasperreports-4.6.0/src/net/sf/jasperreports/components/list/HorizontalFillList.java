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
package net.sf.jasperreports.components.list;

import java.util.ArrayList;
import java.util.List;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.component.FillPrepareResult;
import net.sf.jasperreports.engine.fill.JRFillObjectFactory;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * Horizontal fill list component implementation.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: HorizontalFillList.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class HorizontalFillList extends BaseFillList
{
	
	private static final Log log = LogFactory.getLog(HorizontalFillList.class);
	
	private final int contentsWidth;
	private final boolean ignoreWidth;
	private final List<FillListContents> contentsList;

	private int overflowStartPage;
	private int overflowColumnIndex;
	
	public HorizontalFillList(ListComponent component, JRFillObjectFactory factory) throws JRException
	{
		super(component, factory);
		
		ListContents listContents = component.getContents();
		this.contentsWidth = listContents.getWidth().intValue();
		
		Boolean listIgnoreWidth = component.getIgnoreWidth();
		this.ignoreWidth = listIgnoreWidth != null && listIgnoreWidth.booleanValue();
		
		JRFillObjectFactory datasetFactory = new JRFillObjectFactory(factory, 
				createDatasetExpressionEvaluator());
		FillListContents fillContents = new FillListContents(
				listContents, datasetFactory);
		this.contentsList = new ArrayList<FillListContents>();
		this.contentsList.add(fillContents);
	}

	public FillPrepareResult prepare(int availableHeight)
	{
		createPrintFrame();
		try
		{
			boolean hadData = false;
			
			if (filling)
			{
				// refill row after an overflow
				if (log.isDebugEnabled())
				{
					log.debug("Refilling list row after overflow");
				}
				
				hadData = true;
			}
			else
			{
				if (log.isDebugEnabled())
				{
					log.debug("Starting list rendering");
				}
				
				if (fillStarted)
				{
					// if already started and finished, rewind the data source
					if (log.isDebugEnabled())
					{
						log.debug("List reprinted, rewinding data source");
					}
					
					datasetRun.rewind();
				}
				
				datasetRun.start();
				fillStarted = true;
				
				// reset the overflow page
				overflowStartPage = 0;
			}
			
			boolean overflow = false;
			int columnIndex = 0;
			
			// also breaks when there are no more records, see below
			while(!overflow)
			{
				int contentsAvailableHeight = availableHeight 
						- printFrame.getHeight();
				if (contentsAvailableHeight < contentsHeight)
				{
					// no space left to start a new row
					if (log.isDebugEnabled())
					{
						log.debug("Not enough space left for a list row, overflowing");
					}
					
					overflow = true;
				}
				else
				{
					boolean refillOverflowed = columnIndex < overflowColumnIndex;
					if (!refillOverflowed && !datasetRun.next())
					{
						// no more records
						break;
					}
					
					FillListContents listContents = getContents(columnIndex);
					// we still have overflowed cells to refill
					if (refillOverflowed)
					{
						// refilling an overflowed cell
						if (log.isDebugEnabled())
						{
							log.debug("Refilling overflowed cell at column " 
									+ columnIndex);
						}
						
						listContents.rewind();
					}
					else
					{
						hadData = true;
						
						// reset the overflow counter as we render a new cell
						overflowStartPage = 0;

						// a new cell, evaluate
						if (log.isDebugEnabled())
						{
							log.debug("Filling new cell at column " 
									+ columnIndex);
						}
						
						listContents.evaluateContents();
					}

					listContents.prepare(contentsAvailableHeight);
					overflow = listContents.willOverflow();
					
					++columnIndex;
					
					if (!overflow
							// reached the right element limit
							&& !ignoreWidth
							&& contentsWidth * (columnIndex + 1) 
							> fillContext.getComponentElement().getWidth())
					{
						fillRow(columnIndex);
						
						// reset column index
						columnIndex = 0;
						
						// also reset overflow column index
						overflowColumnIndex = 0;
					}
				}
			}

			if (overflow)
			{
				if (log.isDebugEnabled())
				{
					log.debug("List has overflowed at column " + (columnIndex - 1));
				}
				
				int pageCount = fillContext.getFiller().getCurrentPageCount();
				if (overflowStartPage == 0)
				{
					// first overflow
					overflowStartPage = pageCount;
				}
				else if (pageCount >= overflowStartPage + 2)
				{
					throw new JRRuntimeException("List row overflowed on 3 consecutive pages, "
							+ "likely infinite loop");
				}
				
				// set the filling flag so that we know that we are continuing
				filling = true;
				
				// set the index of the column that overflowed
				// this is actually the index + 1
				overflowColumnIndex = columnIndex;

				return FillPrepareResult.printStretch(availableHeight, overflow);
			}
			else
			{
				// list has completed;
				
				// fill last row, if not filled
				if (columnIndex > 0)
				{
					fillRow(columnIndex);
					
					// reset overflow column index
					overflowColumnIndex = 0;
				}
				
				if (log.isDebugEnabled())
				{
					log.debug("List has completed rendering");
				}
				
				filling = false;
				datasetRun.end();
				
				if (!hadData)
				{
					//if no data, set as no print
					return FillPrepareResult.NO_PRINT_NO_OVERFLOW;
				}

				return FillPrepareResult.printStretch(printFrame.getHeight(), false);
			}
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}

	protected void fillRow(int columnCount) throws JRException
	{
		// completed a row
		// fill all cells from the row
		
		if (log.isDebugEnabled())
		{
			log.debug("Completed a row with " + columnCount + " columns");
		}
		
		AppendingPrintElementContainer printContainer = 
			new AppendingPrintElementContainer(printFrame);

		// compute the row height
		int rowHeight = 0;
		for (int idx = 0; idx < columnCount; ++idx)
		{
			FillListContents contents = contentsList.get(idx);
			int cellHeight = contents.getStretchHeight();
			if (cellHeight > rowHeight)
			{
				rowHeight = cellHeight;
			}
		}
		
		for (int idx = 0; idx < columnCount; ++idx)
		{
			FillListContents contents = contentsList.get(idx);
			// stretch all cells to row height
			contents.stretchTo(rowHeight);
			contents.finalizeElementPositions();
			
			// fill elements
			printContainer.setXOffset(idx * contentsWidth);
			contents.fillElements(printContainer);
		}
	}

	protected FillListContents getContents(int columnIndex)
	{
		if (columnIndex > 0 && columnIndex >= contentsList.size())
		{
			FillListContents template = contentsList.get(0);
			for (int idx = contentsList.size(); idx <= columnIndex; idx++)
			{
				if (log.isDebugEnabled())
				{
					log.debug("Cloning list fill contents for column " + idx);
				}
				
				FillListContents clone = template.createClone();
				contentsList.add(clone);
			}
		}
		return contentsList.get(columnIndex);
	}

	public void rewind()
	{
		super.rewind();

		overflowStartPage = 0;
		overflowColumnIndex = 0;
	}
}
