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
package net.sf.jasperreports.engine.type;

import net.sf.jasperreports.engine.JRConstants;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: PrintOrderEnum.java 4595 2011-09-08 15:55:10Z teodord $
 */
public enum PrintOrderEnum implements JREnum
{
	/**
	 * Specifies that columns in a report should be filled vertically (fill an entire column and then go to the
	 * next one).
	 */
	VERTICAL((byte)1, "Vertical"),

	/**
	 * Specifies that columns in a report should be filled horizontally (columns are filled proportionally).
	 */
	HORIZONTAL((byte)2, "Horizontal");
	
	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	private final transient byte value;
	private final transient String name;

	private PrintOrderEnum(byte value, String name)
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
	public static PrintOrderEnum getByName(String name)
	{
		return (PrintOrderEnum)EnumUtil.getByName(values(), name);
	}
	
	/**
	 *
	 */
	public static PrintOrderEnum getByValue(Byte value)
	{
		return (PrintOrderEnum)EnumUtil.getByValue(values(), value);
	}
	
	/**
	 *
	 */
	public static PrintOrderEnum getByValue(byte value)
	{
		return getByValue(new Byte(value));
	}

}
