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
package net.sf.jasperreports.engine.base;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.JRRectangle;
import net.sf.jasperreports.engine.JRVisitor;
import net.sf.jasperreports.engine.util.JRStyleResolver;


/**
 * The actual implementation of a graphic element representing a rectangle.
 *
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRBaseRectangle.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRBaseRectangle extends JRBaseGraphicElement implements JRRectangle
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	/**
	 *
	 */
	protected Integer radius;


	/**
	 * Initializes properties that are specific to rectangles. Common properties are initialized by its
	 * parent constructors.
	 * @param rectangle an element whose properties are copied to this element. Usually it is a
	 * {@link net.sf.jasperreports.engine.design.JRDesignRectangle} that must be transformed into an
	 * <tt>JRBaseRectangle</tt> at compile time.
	 * @param factory a factory used in the compile process
	 */
	protected JRBaseRectangle(JRRectangle rectangle, JRBaseObjectFactory factory)
	{
		super(rectangle, factory);

		radius = rectangle.getOwnRadius();
	}


	/**
	 *
	 */
	public int getRadius()
	{
		return JRStyleResolver.getRadius(this);
	}

	public Integer getOwnRadius()
	{
		return this.radius;
	}

	/**
	 *
	 */
	public void setRadius(int radius)
	{
		setRadius(Integer.valueOf(radius));
	}

	/**
	 *
	 */
	public void setRadius(Integer radius)
	{
		Object old = this.radius;
		this.radius = radius;
		getEventSupport().firePropertyChange(JRBaseStyle.PROPERTY_RADIUS, old, this.radius);
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
		visitor.visitRectangle(this);
	}


}
