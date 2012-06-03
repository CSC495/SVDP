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
package net.sf.jasperreports.engine.fill;

import java.util.Comparator;

import net.sf.jasperreports.engine.JRPrintElement;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRYXComparator.java 5214 2012-04-03 14:53:44Z teodord $
 */
public class JRYXComparator implements Comparator<JRPrintElement>
{
	

	/**
	 *
	 */
	public int compare(JRPrintElement elem1, JRPrintElement elem2)
	{
		if (elem1.getY() == elem2.getY())
		{
			return elem1.getX() - elem2.getX();
		}
		else
		{
			return elem1.getY() - elem2.getY();
		}
	}


}
