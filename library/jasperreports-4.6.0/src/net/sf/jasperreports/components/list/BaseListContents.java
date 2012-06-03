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

import net.sf.jasperreports.engine.base.JRBaseElementGroup;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;

/**
 * {@link ListContents} implementation used in compiled reports.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: BaseListContents.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class BaseListContents extends JRBaseElementGroup implements ListContents
{

	private static final long serialVersionUID = 1L;
	
	private final int height;
	private final Integer width;

	protected BaseListContents(ListContents listContents, JRBaseObjectFactory factory)
	{
		super(listContents, factory);
		
		this.height = listContents.getHeight();
		this.width = listContents.getWidth();
	}

	public int getHeight()
	{
		return height;
	}

	public Integer getWidth()
	{
		return width;
	}
	
}
