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
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ImageTypeEnum.java 5180 2012-03-29 13:23:12Z teodord $
 */
public enum ImageTypeEnum implements JREnum
{
	/**
	 * Specifies that the image is of unknown type.
	 */ 
	UNKNOWN((byte)0, "Unknown", null),
	
	/**
	 * Specifies that the image is of GIF type.
	 */ 
	GIF((byte)1, "GIF", "image/gif"),
	
	/**
	 * Specifies that the image is of JPEG type.
	 */ 
	JPEG((byte)2, "JPEG", "image/jpeg"),
	
	/**
	 * Specifies that the image is of PNG type.
	 */ 
	PNG((byte)3, "PNG", "image/png"),
	
	/**
	 * Specifies that the image is of TIFF type.
	 */ 
	TIFF((byte)3, "TIFF", "image/tiff");

	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	private final transient byte value;
	private final transient String name;
	private final transient String mimeType;

	private ImageTypeEnum(byte value, String name, String mimeType)
	{
		this.value = value;
		this.name = name;
		this.mimeType = mimeType;
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
	public String getMimeType()
	{
		return mimeType;
	}
	
	/**
	 *
	 */
	public static ImageTypeEnum getByName(String name)
	{
		return (ImageTypeEnum)EnumUtil.getByName(values(), name);
	}
	
	/**
	 *
	 */
	public static ImageTypeEnum getByValue(Byte value)
	{
		return (ImageTypeEnum)EnumUtil.getByValue(values(), value);
	}
	
	/**
	 *
	 */
	public static ImageTypeEnum getByValue(byte value)
	{
		return getByValue(new Byte(value));
	}
	
}
