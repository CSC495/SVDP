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
 * Determines the field and variables values to be used when evaluating an expression.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: EvaluationType.java 4600 2011-09-12 10:32:17Z teodord $
 */
//FIXME deprecate methods that take byte evaluation type in favor of this enum
public enum EvaluationType
{
	/**
	 * Use current values when evaluating the expression.
	 */
	DEFAULT(JRExpression.EVALUATION_DEFAULT),
	/**
	 * Use old/previous values when evaluating the expression.
	 */
	OLD(JRExpression.EVALUATION_OLD),
	/**
	 * Use estimated/future values when evaluating the expression.
	 */
	ESTIMATED(JRExpression.EVALUATION_ESTIMATED);
	
	private final byte type;
	
	private EvaluationType(byte type)
	{
		this.type = type;
	}

	/**
	 * Returns the corresponding byte value for the evaluation type.
	 * 
	 * @see JRExpression#EVALUATION_DEFAULT
	 * @see JRExpression#EVALUATION_OLD
	 * @see JRExpression#EVALUATION_ESTIMATED
	 */
	public byte getType()
	{
		return this.type;
	}
}
