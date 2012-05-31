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
package net.sf.jasperreports.engine.export.draw;

import java.awt.Graphics2D;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRPrintEllipse;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.JRPrintLine;
import net.sf.jasperreports.engine.JRPrintRectangle;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRReport;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.PrintElementVisitor;
import net.sf.jasperreports.engine.export.AwtTextRenderer;
import net.sf.jasperreports.engine.export.GenericElementGraphics2DHandler;
import net.sf.jasperreports.engine.export.GenericElementHandlerEnviroment;
import net.sf.jasperreports.engine.export.JRGraphics2DExporter;
import net.sf.jasperreports.engine.util.JRStyledText;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * Print element draw visitor.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: PrintDrawVisitor.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class PrintDrawVisitor implements PrintElementVisitor<Offset>
{
	private static final Log log = LogFactory.getLog(PrintDrawVisitor.class);
	
	private Graphics2D grx;
	private final JasperReportsContext jasperReportsContext;
	private final LineDrawer lineDrawer;
	private final RectangleDrawer rectangleDrawer;
	private final EllipseDrawer ellipseDrawer;
	private final ImageDrawer imageDrawer;
	private TextDrawer textDrawer;
	private FrameDrawer frameDrawer;

	public PrintDrawVisitor(JasperReportsContext jasperReportsContext)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.lineDrawer = new LineDrawer(jasperReportsContext);
		this.rectangleDrawer = new RectangleDrawer(jasperReportsContext);
		this.ellipseDrawer = new EllipseDrawer(jasperReportsContext);
		this.imageDrawer = new ImageDrawer(jasperReportsContext);
	}
	
	/**
	 * @deprecated Replaced by {@link #PrintDrawVisitor(JasperReportsContext)}.
	 */
	public PrintDrawVisitor()
	{
		this(DefaultJasperReportsContext.getInstance());
	}
	
	public void setTextRenderer(JRReport report)
	{
		AwtTextRenderer textRenderer = 
			new AwtTextRenderer(
				JRPropertiesUtil.getInstance(jasperReportsContext).getBooleanProperty(report, JRGraphics2DExporter.MINIMIZE_PRINTER_JOB_SIZE, true),
				JRPropertiesUtil.getInstance(jasperReportsContext).getBooleanProperty(report, JRStyledText.PROPERTY_AWT_IGNORE_MISSING_FONT, false)
				);
		
		textDrawer = new TextDrawer(jasperReportsContext, textRenderer);
		frameDrawer = new FrameDrawer(jasperReportsContext, null, textRenderer);
	}

	public void setTextDrawer(TextDrawer textDrawer)
	{
		this.textDrawer = textDrawer;
	}

	public void setFrameDrawer(FrameDrawer frameDrawer)
	{
		this.frameDrawer = frameDrawer;
	}

	public void setClip(boolean isClip)
	{
		frameDrawer.setClip(isClip);
	}
	
	public void setGraphics2D(Graphics2D grx)
	{
		this.grx = grx;
	}

	public void visit(JRPrintText textElement, Offset offset)
	{
		textDrawer.draw(
				grx,
				textElement, 
				offset.getX(), 
				offset.getY()
				);
	}

	public void visit(JRPrintImage image, Offset offset)
	{
		try
		{
			imageDrawer.draw(
					grx,
					image, 
					offset.getX(), 
					offset.getY()
					);
		} 
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}

	public void visit(JRPrintRectangle rectangle, Offset offset)
	{
		rectangleDrawer.draw(
				grx,
				rectangle, 
				offset.getX(), 
				offset.getY()
				);
	}

	public void visit(JRPrintLine line, Offset offset)
	{
		lineDrawer.draw(
				grx,
				line, 
				offset.getX(), 
				offset.getY()
				);
	}

	public void visit(JRPrintEllipse ellipse, Offset offset)
	{
		ellipseDrawer.draw(
				grx,
				ellipse, 
				offset.getX(), 
				offset.getY()
				);
	}

	public void visit(JRPrintFrame frame, Offset offset)
	{
		try
		{
			frameDrawer.draw(
				grx,
				frame, 
				offset.getX(), 
				offset.getY()
				);
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}

	public void visit(JRGenericPrintElement printElement, Offset offset)
	{
		GenericElementGraphics2DHandler handler = 
			(GenericElementGraphics2DHandler)GenericElementHandlerEnviroment.getInstance(jasperReportsContext).getElementHandler(
					printElement.getGenericType(), 
					JRGraphics2DExporter.GRAPHICS2D_EXPORTER_KEY
					);

		if (handler != null)
		{
			handler.exportElement(this.frameDrawer.getExporterContext(), printElement, grx, offset);
		}
		else
		{
			if (log.isDebugEnabled())
			{
				log.debug("No Graphics2D generic element handler for " 
						+ printElement.getGenericType());
			}
		}
	}

	/**
	 * @return the imageDrawer
	 */
	public ImageDrawer getImageDrawer()
	{
		return this.imageDrawer;
	}

}
