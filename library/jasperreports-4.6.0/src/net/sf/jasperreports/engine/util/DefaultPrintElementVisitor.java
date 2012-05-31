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

import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRPrintEllipse;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.JRPrintLine;
import net.sf.jasperreports.engine.JRPrintRectangle;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.PrintElementVisitor;

/**
 * Base print element visitor implementation with empty methods.
 * 
 * The class is meant to be extended by visitors that only want to implement specific methods.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: DefaultPrintElementVisitor.java 4732 2011-10-21 09:14:15Z lucianc $
 */
public abstract class DefaultPrintElementVisitor<T> implements
		PrintElementVisitor<T>
{
	
	public void visit(JRPrintText textElement, T arg)
	{
		//NOP
	}

	public void visit(JRPrintImage image, T arg)
	{
		//NOP
	}

	public void visit(JRPrintRectangle rectangle, T arg)
	{
		//NOP
	}

	public void visit(JRPrintLine line, T arg)
	{
		//NOP
	}

	public void visit(JRPrintEllipse ellipse, T arg)
	{
		//NOP
	}

	public void visit(JRPrintFrame frame, T arg)
	{
		//NOP
	}

	public void visit(JRGenericPrintElement printElement, T arg)
	{
		//NOP
	}

}
