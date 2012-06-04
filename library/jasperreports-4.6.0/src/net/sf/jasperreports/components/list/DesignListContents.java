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
package net.sf.jasperreports.components.list;

import net.sf.jasperreports.engine.design.JRDesignElementGroup;

/**
 * {@link ListContents} implementation used at report design time.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: DesignListContents.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class DesignListContents extends JRDesignElementGroup implements
		ListContents
{
	
	private static final long serialVersionUID = 1L;
	
	public static final String PROPERTY_HEIGHT = "height";
	public static final String PROPERTY_WIDTH = "width";
	
	private int height;
	private Integer width;

	public int getHeight()
	{
		return height;
	}
	
	/**
	 * Sets the list contents height.
	 * 
	 * @param height the list contents height
	 * @see #getHeight()
	 */
	public void setHeight(int height)
	{
		int old = this.height;
		this.height = height;
		getEventSupport().firePropertyChange(PROPERTY_HEIGHT, old, this.height);
	}

	public Integer getWidth()
	{
		return width;
	}

	/**
	 * Sets the width of the list contents.
	 * 
	 * @param width the list contents width
	 * @see #getWidth()
	 */
	public void setWidth(Integer width)
	{
		Object old = this.width;
		this.width = width;
		getEventSupport().firePropertyChange(PROPERTY_WIDTH, old, this.width);
	}

	/**
	 * Sets the width of the list contents.
	 * 
	 * @param width the list contents width
	 * @see #getWidth()
	 */
	public void setWidth(int width)
	{
		setWidth(Integer.valueOf(width));
	}

}
