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
package net.sf.jasperreports.charts;

import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRHyperlink;

/**
 * @author Peter Risko (peter@risko.hu)
 * @version $Id: JRGanttSeries.java 4595 2011-09-08 15:55:10Z teodord $
 */
public interface JRGanttSeries {

	/**
	 *
	 */
	public JRExpression getSeriesExpression();

	/**
	 *
	 */
	public JRExpression getTaskExpression();

	/**
	 *
	 */
	public JRExpression getSubtaskExpression();

	/**
	 *
	 */
	public JRExpression getStartDateExpression();

	/**
	 *
	 */
	public JRExpression getEndDateExpression();

	/**
	 *
	 */
	public JRExpression getPercentExpression();

	/**
	 *
	 */
	public JRExpression getLabelExpression();


	/**
	 * Returns the hyperlink specification for chart items.
	 * <p>
	 * The hyperlink will be evaluated for every chart item and an image map will be created for the chart.
	 * </p>
	 *
	 * @return hyperlink specification for chart items
	 */
	public JRHyperlink getItemHyperlink();

}
