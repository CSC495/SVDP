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
package net.sf.jasperreports.charts.type;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.type.EnumUtil;
import net.sf.jasperreports.engine.type.JREnum;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: ValueLocationEnum.java 4595 2011-09-08 15:55:10Z teodord $
 */
public enum ValueLocationEnum implements JREnum
{
	/**
	 * The value should not be displayed.
	 */
	NONE((byte)0, "none"),
	
	/**
	 * The value should be displayed to the left of the thermometer.
	 */
	LEFT((byte)1, "left"),
	
	/**
	 * The value should be displayed to the right of the thermometer.
	 */
	RIGHT((byte)2, "right"),
	
	/**
	 * The value should be displayed in the bulb of the thermometer.  When
	 * using this option make sure the font is small enough or the value short
	 * enough so the value fits in the bulb.
	 */
	BULB((byte)3, "bulb");
	

	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	private final transient byte value;
	private final transient String name;

	private ValueLocationEnum(byte value, String name)
	{
		this.value = value;
		this.name = name;
	}

	/**
	 *
	 */
	public Byte getValueByte()
	{
		return new Byte(value);
	}
	
	/**
	 *
	 */
	public final byte getValue()
	{
		return value;
	}
	
	/**
	 *
	 */
	public String getName()
	{
		return name;
	}
	
	/**
	 *
	 */
	public static ValueLocationEnum getByName(String name)
	{
		return (ValueLocationEnum)EnumUtil.getByName(values(), name);
	}
	
	/**
	 *
	 */
	public static ValueLocationEnum getByValue(Byte value)
	{
		return (ValueLocationEnum)EnumUtil.getByValue(values(), value);
	}
	
	/**
	 *
	 */
	public static ValueLocationEnum getByValue(byte value)
	{
		return getByValue(new Byte(value));
	}

}
