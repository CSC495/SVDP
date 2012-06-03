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
 * An abstract representation of a report element. All report elements implement this interface. The interface contains
 * constants and methods that apply to all report elements.
 *
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRChild.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRChild extends JRVisitable, JRCloneable
{
	/**
	 * 
	 */
	public Object clone(JRElementGroup parentGroup);
}
