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
package net.sf.jasperreports.engine.export.draw;

/**
 * Drawing offset used by the print element draw visitor.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: Offset.java 4595 2011-09-08 15:55:10Z teodord $
 * @see PrintDrawVisitor
 */
public class Offset
{

	private final int x;
	private final int y;
	
	/**
	 * Creates an offset object.
	 * 
	 * @param x the horizontal offset
	 * @param y the vertical offset
	 */
	public Offset(int x, int y)
	{
		this.x = x;
		this.y = y;
	}

	/**
	 * Returns the horizontal offset.
	 * 
	 * @return the horizontal offset
	 */
	public int getX()
	{
		return x;
	}

	/**
	 * Returns the vertical offset.
	 * 
	 * @return the vertical offset
	 */
	public int getY()
	{
		return y;
	}
	
}
