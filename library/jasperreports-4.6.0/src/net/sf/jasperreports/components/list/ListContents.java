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

import net.sf.jasperreports.engine.JRElementGroup;

/**
 * Container of report elements that are to be printed for each record in the
 * list subdataset.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: ListContents.java 4595 2011-09-08 15:55:10Z teodord $
 * @see ListComponent#getContents()
 */
public interface ListContents extends JRElementGroup
{

	/**
	 * Returns the height of the list contents.
	 * 
	 * <p>
	 * This value is used as a minimum list item height: if the space left on
	 * the page is smaller than the height, then an overflow is triggered and
	 * the list item is printed on a new page/column.
	 * 
	 * @return the height of the list contents
	 */
	int getHeight();

	/**
	 * Returns the width of the list contents.
	 * 
	 * <p>
	 * When the list is filled horizontally, several list cells will be placed
	 * on the same row one next to another within the width of the list element
	 * (unless the list element width is ignored).
	 * The width of the list contents will determine how many cells can be
	 * placed on a row.
	 * </p>
	 * 
	 * @return the width of the list contents
	 * @see ListComponent#getPrintOrderValue()
	 * @see ListComponent#getIgnoreWidth()
	 */
	Integer getWidth();
}
