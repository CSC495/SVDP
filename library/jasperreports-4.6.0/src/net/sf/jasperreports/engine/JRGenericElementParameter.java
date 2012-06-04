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
 * A generic report element parameter.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRGenericElementParameter.java 4595 2011-09-08 15:55:10Z teodord $
 * @see JRGenericElement#getParameters()
 */
public interface JRGenericElementParameter extends JRCloneable
{

	/**
	 * Returns the name of the parameter.
	 * 
	 * <p>
	 * The name will be propagated into the generic print element, as in
	 * {@link JRGenericPrintElement#setParameterValue(String, Object)}.
	 * 
	 * @return the name of the parameter
	 */
	String getName();
	
	/**
	 * Returns the expression that provides parameter values.
	 * 
	 * <p>
	 * The result of the expression evaluation will be propagated into the
	 * generic print element as parameter value, as in
	 * {@link JRGenericPrintElement#setParameterValue(String, Object)}.
	 * 
	 * @return the parameter's value expression
	 */
	JRExpression getValueExpression();
	
	/**
	 * Decides whether the parameter is skipped when its value evaluates to
	 * <code>null</code>.
	 * 
	 * <p>
	 * When the parameter's expression evaluates to <code>null</code> and this
	 * flag is set and , the parameter is not included in the generated print
	 * element.  If the flag is not set, the parameter is included with a
	 * <code>null</code> value. 
	 * 
	 * @return whether the parameter is skipped when its value is
	 * <code>null</code>
	 */
	boolean isSkipWhenEmpty();
	
}
