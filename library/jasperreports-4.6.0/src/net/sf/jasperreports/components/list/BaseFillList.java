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

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPrintElementContainer;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.component.BaseFillComponent;
import net.sf.jasperreports.engine.fill.JRFillDataset;
import net.sf.jasperreports.engine.fill.JRFillExpressionEvaluator;
import net.sf.jasperreports.engine.fill.JRFillObjectFactory;
import net.sf.jasperreports.engine.fill.JRTemplateFrame;
import net.sf.jasperreports.engine.fill.JRTemplatePrintFrame;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * Base fill list component implementation.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: BaseFillList.java 4648 2011-10-10 12:50:53Z lucianc $
 */
public abstract class BaseFillList extends BaseFillComponent
{
	
	private static final Log log = LogFactory.getLog(BaseFillList.class);
	
	protected final int contentsHeight;
	protected final FillDatasetRun datasetRun;
	
	protected Map<JRStyle, JRTemplateFrame> printFrameTemplates = new HashMap<JRStyle, JRTemplateFrame>();
	protected JRTemplatePrintFrame printFrame;
	protected boolean filling;
	protected boolean fillStarted;
	
	public BaseFillList(ListComponent component, JRFillObjectFactory factory) throws JRException
	{
		ListContents listContents = component.getContents();
		this.contentsHeight = listContents.getHeight();
		
		this.datasetRun = new FillDatasetRun(component.getDatasetRun(), factory);
	}

	protected JRFillExpressionEvaluator createDatasetExpressionEvaluator()
	{
		return new JRFillExpressionEvaluator()
		{
			public Object evaluate(JRExpression expression,
					byte evaluationType) throws JRException
			{
				return datasetRun.evaluateDatasetExpression(
						expression, evaluationType);
			}

			public JRFillDataset getFillDataset()
			{
				return datasetRun.getDataset();
			}
		};
	}

	public void evaluate(byte evaluation) throws JRException
	{
		if (filling)
		{
			log.warn("List fill did not complete, closing previous dataset run");
			datasetRun.end();
		}
		
		filling = false;
		fillStarted = false;
		
		datasetRun.evaluate(evaluation);
	}

	protected void createPrintFrame()
	{
		printFrame = new JRTemplatePrintFrame(getFrameTemplate(), elementId);
		printFrame.setX(fillContext.getComponentElement().getX());
		printFrame.setWidth(fillContext.getComponentElement().getWidth());
	}

	protected JRTemplateFrame getFrameTemplate()
	{
		JRStyle style = fillContext.getElementStyle();
		JRTemplateFrame frameTemplate = printFrameTemplates.get(style);
		if (frameTemplate == null)
		{
			frameTemplate = new JRTemplateFrame(
						fillContext.getElementOrigin(),
						fillContext.getDefaultStyleProvider());
			frameTemplate.setElement(fillContext.getComponentElement());
			frameTemplate = deduplicate(frameTemplate);
			
			printFrameTemplates.put(style, frameTemplate);
		}

		return frameTemplate;
	}

	public JRPrintElement fill()
	{
		printFrame.setY(fillContext.getElementPrintY());
		return printFrame;
	}

	public void rewind()
	{
		try
		{
			if (filling)
			{
				// if currently running, close the query
				datasetRun.end();
			}
			
			if (fillStarted)
			{
				if (log.isDebugEnabled())
				{
					log.debug("Rewinding started list");
				}
				
				// rewind the data source
				datasetRun.rewind();
			}
				
			filling = false;
			fillStarted = false;
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}

	protected static class AppendingPrintElementContainer implements JRPrintElementContainer
	{
		private final JRPrintElementContainer container;
		private final int initialContainerHeight;
		private int xOffset;

		public AppendingPrintElementContainer(JRPrintElementContainer container)
		{
			this.container = container;
			this.initialContainerHeight = container.getHeight();
		}
		
		public void addElement(JRPrintElement element)
		{
			if (xOffset > 0)
			{
				element.setX(element.getX() + xOffset);
			}
			
			element.setY(initialContainerHeight + element.getY());
			container.addElement(element);
		}

		public List<JRPrintElement> getElements()
		{
			return container.getElements();
		}

		public int getHeight()
		{
			throw new UnsupportedOperationException();
		}

		public void setHeight(int height)
		{
			int newHeight = initialContainerHeight + height;
			if (newHeight > container.getHeight())
			{
				container.setHeight(newHeight);
			}
		}

		public void setXOffset(int xOffset)
		{
			this.xOffset = xOffset;
		}
	}
}
