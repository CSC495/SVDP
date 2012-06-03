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
package net.sf.jasperreports.engine;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRExpressionChunk.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRExpressionChunk extends JRCloneable
{


	/**
	 *
	 */
	public static final byte TYPE_TEXT = 1;
	public static final byte TYPE_PARAMETER = 2;
	public static final byte TYPE_FIELD = 3;
	public static final byte TYPE_VARIABLE = 4;
	public static final byte TYPE_RESOURCE = 5;


	/**
	 *
	 */
	public byte getType();
		
	/**
	 *
	 */
	public String getText();
		

}
