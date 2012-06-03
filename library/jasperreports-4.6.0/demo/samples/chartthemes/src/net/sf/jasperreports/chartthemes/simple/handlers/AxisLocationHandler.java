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
package net.sf.jasperreports.chartthemes.simple.handlers;

import org.exolab.castor.mapping.GeneralizedFieldHandler;
import org.jfree.chart.axis.AxisLocation;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: AxisLocationHandler.java 5250 2012-04-10 12:29:57Z teodord $
 */
public class AxisLocationHandler extends GeneralizedFieldHandler
{
	/**
	 *
	 */
	public AxisLocationHandler()
	{
		super();
	}
	
	/**
	 *
	 */
	public Object convertUponGet(Object value)
	{
		if (value == null)
		{
			return null;
		}
		return ((AxisLocation)value).toString();
	}

	/**
	 *
	 */
	public Object convertUponSet(Object value)
	{
		if (value == null)
		{
			return null;
		}
		return 
		AxisLocation.BOTTOM_OR_LEFT.toString().equals(value) 
		? AxisLocation.BOTTOM_OR_LEFT 
		: AxisLocation.BOTTOM_OR_RIGHT.toString().equals(value)
		? AxisLocation.BOTTOM_OR_RIGHT
		: AxisLocation.TOP_OR_LEFT.toString().equals(value)
		? AxisLocation.TOP_OR_LEFT
		: AxisLocation.TOP_OR_RIGHT.toString().equals(value)
		? AxisLocation.TOP_OR_RIGHT : null;
	}
	
	/**
	 *
	 */
	public Class<?> getFieldType()
	{
		return AxisLocation.class;
	}

	/**
	 *
	 */
	public Object newInstance(Object parent) throws IllegalStateException
	{
		//-- Since it's marked as a string...just return null,
		//-- it's not needed.
		return null;
	}
}
