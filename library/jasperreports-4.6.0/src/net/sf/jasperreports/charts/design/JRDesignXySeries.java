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
package net.sf.jasperreports.charts.design;

import net.sf.jasperreports.charts.base.JRBaseXySeries;
import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRHyperlink;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;



/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRDesignXySeries.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRDesignXySeries extends JRBaseXySeries implements JRChangeEventsSupport
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	public static final String PROPERTY_ITEM_HYPERLINK = "itemHyperlink";
	
	public static final String PROPERTY_LABEL_EXPRESSION = "labelExpression";
	
	public static final String PROPERTY_SERIES_EXPRESSION = "seriesExpression";
	
	public static final String PROPERTY_X_VALUE_EXPRESSION = "xValueExpression";
	
	public static final String PROPERTY_Y_VALUE_EXPRESSION = "yValueExpression";

	
	/**
	 *
	 */
	public void setSeriesExpression(JRExpression seriesExpression)
	{
		Object old = this.seriesExpression;
		this.seriesExpression = seriesExpression;
		getEventSupport().firePropertyChange(PROPERTY_SERIES_EXPRESSION, old, this.seriesExpression);
	}

	/**
	 *
	 */
	public void setXValueExpression(JRExpression xValueExpression)
	{
		Object old = this.xValueExpression;
		this.xValueExpression = xValueExpression;
		getEventSupport().firePropertyChange(PROPERTY_X_VALUE_EXPRESSION, old, this.xValueExpression);
	}

	/**
	 *
	 */
	public void setYValueExpression(JRExpression yValueExpression)
	{
		Object old = this.yValueExpression;
		this.yValueExpression = yValueExpression;
		getEventSupport().firePropertyChange(PROPERTY_Y_VALUE_EXPRESSION, old, this.yValueExpression);
	}

	/**
	 *
	 */
	public void setLabelExpression(JRExpression labelExpression)
	{
		Object old = this.labelExpression;
		this.labelExpression = labelExpression;
		getEventSupport().firePropertyChange(PROPERTY_LABEL_EXPRESSION, old, this.labelExpression);
	}


	/**
	 * Sets the hyperlink specification for chart items.
	 * 
	 * @param itemHyperlink the hyperlink specification
	 * @see #getItemHyperlink()
	 */
	public void setItemHyperlink(JRHyperlink itemHyperlink)
	{
		Object old = this.itemHyperlink;
		this.itemHyperlink = itemHyperlink;
		getEventSupport().firePropertyChange(PROPERTY_ITEM_HYPERLINK, old, this.itemHyperlink);
	}

	/**
	 *
	 */
	public Object clone()
	{
		JRDesignXySeries clone = (JRDesignXySeries)super.clone();
		clone.eventSupport = null;
		return clone;
	}
	
	private transient JRPropertyChangeSupport eventSupport;
	
	public JRPropertyChangeSupport getEventSupport()
	{
		synchronized (this)
		{
			if (eventSupport == null)
			{
				eventSupport = new JRPropertyChangeSupport(this);
			}
		}
		
		return eventSupport;
	}

}
