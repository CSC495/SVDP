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

import net.sf.jasperreports.charts.type.EdgeEnum;

import org.exolab.castor.mapping.GeneralizedFieldHandler;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: EdgeFieldHandler.java 5250 2012-04-10 12:29:57Z teodord $
 */
public class EdgeFieldHandler extends GeneralizedFieldHandler
{
	/**
	 *
	 */
	public EdgeFieldHandler()
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
//		return JRXmlConstants.getChartEdgeMap().get(value);
//		return EdgeEnum.getByName((String)value);
		return ((EdgeEnum)value).getName();
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
//		return JRXmlConstants.getChartEdgeMap().get(value);
		return EdgeEnum.getByName((String)value);
	}
	
	/**
	 *
	 */
	public Class<?> getFieldType()
	{
		return EdgeEnum.class;
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
