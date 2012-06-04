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

import net.sf.jasperreports.engine.JRChartDataset;

import org.jfree.data.general.Dataset;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRFillChartDataset.java 5180 2012-03-29 13:23:12Z teodord $
 */
public abstract class JRFillChartDataset extends JRFillElementDataset implements JRChartDataset
{
	/**
	 *
	 */
	protected JRFillChartDataset(
		JRChartDataset dataset, 
		JRFillObjectFactory factory
		)
	{
		super(dataset, factory);
	}

	/**
	 *
	 */
	public Dataset getDataset()
	{
		increment();
		
		return getCustomDataset();
	}

	/**
	 *
	 */
	public abstract Dataset getCustomDataset();

	/**
	 *
	 */
	public abstract Object getLabelGenerator();//FIXMETHEME this could return some sort of base label generator interface from JFreeChart
}
