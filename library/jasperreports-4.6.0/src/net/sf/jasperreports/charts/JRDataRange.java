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

import net.sf.jasperreports.engine.JRCloneable;
import net.sf.jasperreports.engine.JRExpression;


/**
 * Represents a numeric range.
 *
 * @author Barry Klawans (bklawans@users.sourceforge.net)
 * @version $Id: JRDataRange.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRDataRange extends JRCloneable
{
	/**
	 * Returns the expression that generates the lower bound of the range.
	 *
	 * @return the expression that generates the lower bound of the range
	 */
	public JRExpression getLowExpression();

	/**
	 * Returns the expression that generates the upper bound of the range.
	 *
	 * @return the expression that generates the upper bound of the range
	 */
	public JRExpression getHighExpression();
}
