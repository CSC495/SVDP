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
package net.sf.jasperreports.components.list;

import java.awt.Color;
import java.awt.Graphics2D;
import java.awt.Shape;
import java.awt.geom.Rectangle2D;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRAbstractSvgRenderer;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * Am image renderer used to mark the unused vertical space in a list
 * component preview.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: UnusedSpaceImageRenderer.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class UnusedSpaceImageRenderer extends JRAbstractSvgRenderer
{

	private static final long serialVersionUID = 1L;

	// transparent light grey
	private static final Color FILL = new Color(224, 224, 224, 128);
	
	public static final UnusedSpaceImageRenderer INSTANCE = new UnusedSpaceImageRenderer();

	private final Shape clip;
	private final int lineGap = 15;
	private final int lineWidth = 10;
	
	public UnusedSpaceImageRenderer()
	{
		this(null);
	}
	
	public UnusedSpaceImageRenderer(Shape clip)
	{
		this.clip = clip;
	}
	
	/**
	 * @deprecated Replaced by {@link #render(JasperReportsContext, Graphics2D, Rectangle2D)}.
	 */
	public void render(Graphics2D grx, Rectangle2D rectangle) throws JRException
	{
		render(DefaultJasperReportsContext.getInstance(), grx, rectangle);	
	}
	
	public void render(
		JasperReportsContext jasperReportsContext,
		Graphics2D grx, 
		Rectangle2D rectangle
		) throws JRException
	{
		Graphics2D graphics = (Graphics2D) grx.create();
		graphics.translate(rectangle.getX(), rectangle.getY());
		graphics.setColor(FILL);
		
		if (clip != null)
		{
			graphics.clip(clip);
		}
		
		int width = (int) rectangle.getWidth();
		int limit = width + (int) rectangle.getHeight();
		int increment = lineGap + lineWidth;
		int reverseOffset = (width - 4 * lineWidth / 3) % increment;
		for (int x = 0; x <= limit; x += increment)
		{
			graphics.fillPolygon(
					new int[]{x, x + lineWidth, 0, 0},
					new int[]{0, 0, x + lineWidth, x},
					4);

			graphics.fillPolygon(
					new int[]{width - x - reverseOffset, width - x - lineWidth - reverseOffset, width, width},
					new int[]{0, 0, x + lineWidth, x},
					4);
		}
	}

}
