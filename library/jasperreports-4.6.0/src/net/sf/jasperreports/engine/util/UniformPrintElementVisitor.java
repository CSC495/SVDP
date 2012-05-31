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
package net.sf.jasperreports.engine.util;

import java.util.List;

import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPrintEllipse;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.JRPrintLine;
import net.sf.jasperreports.engine.JRPrintRectangle;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.PrintElementVisitor;

/**
 * Print element visitor that delegates all visit calls to a single method.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: UniformPrintElementVisitor.java 4732 2011-10-21 09:14:15Z lucianc $
 */
public abstract class UniformPrintElementVisitor<T> implements
		PrintElementVisitor<T>
{

	private final boolean deep;
	
	/**
	 * Creates a uniform visitor.
	 */
	protected UniformPrintElementVisitor()
	{
		this.deep = false;
	}
	
	/**
	 * Creates an optionally deep uniform visitor.
	 * 
	 * @param deep whether elements are to be deeply visited
	 * @see DeepPrintElementVisitor
	 */
	protected UniformPrintElementVisitor(boolean deep)
	{
		this.deep = deep;
	}
	
	protected abstract void visitElement(JRPrintElement element, T arg);
	
	public void visit(JRPrintText textElement, T arg)
	{
		visitElement(textElement, arg);
	}

	public void visit(JRPrintImage image, T arg)
	{
		visitElement(image, arg);
	}

	public void visit(JRPrintRectangle rectangle, T arg)
	{
		visitElement(rectangle, arg);
	}

	public void visit(JRPrintLine line, T arg)
	{
		visitElement(line, arg);
	}

	public void visit(JRPrintEllipse ellipse, T arg)
	{
		visitElement(ellipse, arg);
	}

	public void visit(JRPrintFrame frame, T arg)
	{
		visitElement(frame, arg);
		
		if (deep)
		{
			List<JRPrintElement> elements = frame.getElements();
			if (elements != null)
			{
				for (JRPrintElement element : elements)
				{
					element.accept(this, arg);
				}
			}
		}
	}

	public void visit(JRGenericPrintElement printElement, T arg)
	{
		visitElement(printElement, arg);
	}

}
