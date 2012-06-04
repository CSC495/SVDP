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
package net.sf.jasperreports.crosstabs;

import net.sf.jasperreports.engine.JRElementDataset;

/**
 * Input dataset interface used by crosstabs.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRCrosstabDataset.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRCrosstabDataset extends JRElementDataset
{
	/**
	 * Returns whether the crosstab data is pre-sorted according to the
	 * crosstab's groups.
	 * <p>
	 * The crosstab calculation engine can optimize the calculations
	 * if the data is sorted by the row groups and column groups.
	 * For example, if there are two row groups R1 and R2 (subgroup of R1)
	 * and three column groups C1, C2 and C3 the data should be sorted 
	 * by R1, R2, C1, C2, C3.
	 * 
	 * @return whether the crosstab data is pre-sorted
	 */
	public boolean isDataPreSorted();
}
