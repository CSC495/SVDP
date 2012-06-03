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
package net.sf.jasperreports.engine.component;

import java.util.Locale;
import java.util.ResourceBundle;
import java.util.TimeZone;

import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRDefaultStyleProvider;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JROrigin;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.fill.JRBaseFiller;
import net.sf.jasperreports.engine.fill.JRFillExpressionEvaluator;
import net.sf.jasperreports.engine.type.EvaluationTimeEnum;

/**
 * A fill context provides access to data and functionality related to a
 * report component fill.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: FillContext.java 4808 2011-11-21 13:44:22Z lucianc $
 */
public interface FillContext extends JRFillExpressionEvaluator
{
	
	/**
	 * Returns the component element that wraps the component.
	 * 
	 * @return the current component element
	 */
	JRComponentElement getComponentElement();

	/**
	 * Returns the fill element Id associated to the component element.
	 * 
	 * @return the Id of the component fill element
	 * @see JRPrintElement#getSourceElementId()
	 */
	int getElementSourceId();
	
	/**
	 * Evaluates an expression in the main report dataset.
	 * 
	 * @param expression the expression to evaluate
	 * @param evaluation the evaluation type; usually directly passed from
	 * {@link FillComponent#evaluate(byte)}
	 * @return the result of the evaluation
	 * @throws JRException
	 */
	Object evaluate(JRExpression expression, byte evaluation) throws JRException;
	
	/**
	 * Returns the default style provider for the generated report.
	 * 
	 * @return the default style provider of the generated report
	 */
	JRDefaultStyleProvider getDefaultStyleProvider();
	
	/**
	 * Returns the origin of the current component element.
	 * 
	 * @return the origin of the component element
	 */
	JROrigin getElementOrigin();

	/**
	 * Returns the position on the vertical axis where the component element
	 * starts printing.
	 * 
	 * @return the position on the vertical axis of the component element
	 */
	int getElementPrintY();
	
	/**
	 * Returns the current style of the component element.
	 * 
	 * @return the current style of the component element
	 */
	JRStyle getElementStyle();
	
	/**
	 * Registers a delayed evaluation for a print element.
	 * 
	 * @param printElement the print element
	 * @param evaluationTime the delayed evaluation time; one of
	 * <ul>
	 * 	<li>{@link EvaluationTimeEnum#BAND}
	 * 	<li>{@link EvaluationTimeEnum#COLUMN}
	 * 	<li>{@link EvaluationTimeEnum#PAGE}
	 * 	<li>{@link EvaluationTimeEnum#GROUP}
	 * 	<li>{@link EvaluationTimeEnum#REPORT}
	 * </ul>
	 * @param evaluationGroup the evaluation group name, if
	 * <code>evaluationTime</code> is {@link EvaluationTimeEnum#GROUP}
	 * @see FillComponent#evaluateDelayedElement(JRPrintElement, byte)
	 */
	void registerDelayedEvaluation(JRPrintElement printElement, 
			EvaluationTimeEnum evaluationTime, String evaluationGroup);

	/**
	 * Returns the resource bundle used for the current report.
	 * 
	 * @return the report resource bundle
	 * @see net.sf.jasperreports.engine.JRReport#getResourceBundle()
	 * @see net.sf.jasperreports.engine.JRParameter#REPORT_RESOURCE_BUNDLE
	 */
	ResourceBundle getReportResourceBundle();
	
	/**
	 * Returns the locale used to fill the current report.
	 * 
	 * @return the report locale
	 * @see net.sf.jasperreports.engine.JRParameter#REPORT_LOCALE
	 */
	Locale getReportLocale();
	
	/**
	 * Returns the time zone used to fill the current report.
	 * 
	 * @return the report time zone
	 * @see net.sf.jasperreports.engine.JRParameter#REPORT_TIME_ZONE
	 */
	TimeZone getReportTimezone();

	/**
	 * Returns the filler object.
	 * 
	 * @return the filler object
	 */
	JRBaseFiller getFiller();
	
	//TODO access to params/fields/vars?
}
