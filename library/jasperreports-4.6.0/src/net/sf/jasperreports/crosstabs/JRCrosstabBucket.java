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

import net.sf.jasperreports.engine.JRCloneable;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.type.SortOrderEnum;

/**
 * Crosstab groups bucketing information interface.
 * <p>
 * The bucketing information consists of the grouping expression
 * and sorting information.
 * The buckets can be sorted according to the natural sorting (if the values
 * implement {@link java.lang.Comparable Comparable}) or using a comparator.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRCrosstabBucket.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface JRCrosstabBucket extends JRCloneable
{
	/**
	 * Returns the class of the bucket value. Any class is allowed as long as it is in the classpath at compile and run time.
	 * @return a <tt>Class</tt> instance representing the bucket value class
	 */
	public Class<?> getValueClass();
		
	/**
	 * Returns the string name of the bucket value class.
	 */
	public String getValueClassName();
		
	/**
	 * Returns the bucket sorting type.
	 * <p>
	 * The possible values are:
	 * <ul>
	 * 	<li>{@link SortOrderEnum#ASCENDING SortOrderEnum.ASCENDING}</li>
	 * 	<li>{@link SortOrderEnum#DESCENDING SortOrderEnum.DESCENDING}</li>
	 * </ul>
	 * 
	 * @return the bucket sorting type
	 */
	public SortOrderEnum getOrderValue();
	
	
	/**
	 * Returns the grouping expression.
	 * 
	 * @return the grouping expression
	 */
	public JRExpression getExpression();
	
	
	/**
	 * Returns the comparator expression.
	 * <p>
	 * The result of this expression is used to sort the buckets, in ascending or
	 * descending order (given by {@link #getOrderValue() getOrderValue()}.
	 * If the bucket has an order by expression, the comparator will be used to
	 * compare values as produced by that expression.
	 * If no comparator expression is specified, the natural order will be used.
	 * </p>
	 * 
	 * @return the comparator expression
	 * @see #getOrderByExpression()
	 */
	public JRExpression getComparatorExpression();
	
	
	/**
	 * Returns an expression that provides order by values for group buckets.
	 * If not set, the bucket values as returned by {@link #getExpression()}
	 * are used to order the buckets.
	 * 
	 * <p>
	 * The expression is evaluated in the context of the crosstab group and can
	 * reference measure variables, which evaluate to group totals.
	 * </p>
	 * 
	 * @return the order by value expression for the group bucket
	 */
	public JRExpression getOrderByExpression();
}
