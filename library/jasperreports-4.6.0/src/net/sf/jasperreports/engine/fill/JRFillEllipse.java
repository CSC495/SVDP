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

import net.sf.jasperreports.engine.JREllipse;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRVisitor;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRFillEllipse.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRFillEllipse extends JRFillGraphicElement implements JREllipse
{


	/**
	 *
	 */
	protected JRFillEllipse(
		JRBaseFiller filler,
		JREllipse ellipse, 
		JRFillObjectFactory factory
		)
	{
		super(filler, ellipse, factory);
	}


	protected JRFillEllipse(JRFillEllipse ellipse, JRFillCloneFactory factory)
	{
		super(ellipse, factory);
	}


	/**
	 * 
	 */
	protected JRTemplateEllipse getJRTemplateEllipse()
	{
		return (JRTemplateEllipse) getElementTemplate();
	}

	protected JRTemplateElement createElementTemplate()
	{
		return new JRTemplateEllipse(
				getElementOrigin(), 
				filler.getJasperPrint().getDefaultStyleProvider(), 
				this);
	}


	/**
	 *
	 */
	protected void evaluate(
		byte evaluation
		) throws JRException
	{
		this.reset();
		
		this.evaluatePrintWhenExpression(evaluation);
		evaluateProperties(evaluation);
		
		setValueRepeating(true);
	}


	/**
	 *
	 */
	protected JRPrintElement fill()
	{
		JRTemplatePrintEllipse printEllipse = new JRTemplatePrintEllipse(this.getJRTemplateEllipse(), elementId);
		printEllipse.setX(this.getX());
		printEllipse.setY(this.getRelativeY());
		printEllipse.setWidth(getWidth());
		printEllipse.setHeight(this.getStretchHeight());
		transferProperties(printEllipse);
		
		return printEllipse;
	}


	/**
	 *
	 */
	public void collectExpressions(JRExpressionCollector collector)
	{
		collector.collect(this);
	}

	/**
	 *
	 */
	public void visit(JRVisitor visitor)
	{
		visitor.visitEllipse(this);
	}

	/**
	 *
	 */
	protected void resolveElement (JRPrintElement element, byte evaluation)
	{
		// nothing
	}


	public JRFillCloneable createClone(JRFillCloneFactory factory)
	{
		return new JRFillEllipse(this, factory);
	}

}
