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
package net.sf.jasperreports.data;

import java.util.TimeZone;

import net.sf.jasperreports.engine.util.JRDataUtils;

import org.exolab.castor.mapping.GeneralizedFieldHandler;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: TimeZoneFieldHandler.java 5370 2012-05-11 07:42:15Z teodord $
 */
public class TimeZoneFieldHandler extends GeneralizedFieldHandler
{
	/**
	 *
	 */
	public TimeZoneFieldHandler()
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
		return JRDataUtils.getTimeZoneId((TimeZone)value);
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
		return JRDataUtils.getTimeZone((String)value);
	}
	
	/**
	 *
	 */
	public Class<?> getFieldType()
	{
		return TimeZone.class;//FIXMECONTEXT is this correct?
	}

	/**
	 *
	 */
	public Object newInstance(Object parent) throws IllegalStateException
	{
		return null;
	}
}
