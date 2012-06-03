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
 * @version $Id: OnErrorTypeEnum.java 4595 2011-09-08 15:55:10Z teodord $
 */
public enum OnErrorTypeEnum implements JREnum
{
	/**
	 * A constant used for specifying that the engine should raise an exception if the image is not found.
	 */
	ERROR((byte)1, "Error"),

	/**
	 * A constant used for specifying that the engine should display blank space if the image is not found.
	 */
	BLANK((byte)2, "Blank"),
	
	/**
	 * A constant used for specifying that the engine should display a replacement icon if the image is not found.
	 */
	ICON((byte)3, "Icon");
	
	
	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	private final transient byte value;
	private final transient String name;

	private OnErrorTypeEnum(byte value, String name)
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
	public static OnErrorTypeEnum getByName(String name)
	{
		return (OnErrorTypeEnum)EnumUtil.getByName(values(), name);
	}
	
	/**
	 *
	 */
	public static OnErrorTypeEnum getByValue(Byte value)
	{
		return (OnErrorTypeEnum)EnumUtil.getByValue(values(), value);
	}
	
	/**
	 *
	 */
	public static OnErrorTypeEnum getByValue(byte value)
	{
		return getByValue(new Byte(value));
	}

}
