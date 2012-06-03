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

/*
 * Contributors:
 * Eugene D - eugenedruy@users.sourceforge.net 
 * Adrian Jackson - iapetus@users.sourceforge.net
 * David Taylor - exodussystems@users.sourceforge.net
 * Lars Kristensen - llk@users.sourceforge.net
 */
package net.sf.jasperreports.engine.export.draw;

import java.awt.BasicStroke;
import java.awt.Graphics2D;
import java.awt.Stroke;
import java.awt.geom.AffineTransform;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRPrintRectangle;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.export.legacy.BorderOffset;
import net.sf.jasperreports.engine.type.LineStyleEnum;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.util.JRPenUtil;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: RectangleDrawer.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class RectangleDrawer extends ElementDrawer<JRPrintRectangle>
{
	/**
	 * @deprecated Replaced by {@link #RectangleDrawer(JasperReportsContext)}.
	 */
	public RectangleDrawer()
	{
		this(DefaultJasperReportsContext.getInstance());
	}
	
	
	/**
	 *
	 */
	public RectangleDrawer(JasperReportsContext jasperReportsContext)
	{
		super(jasperReportsContext);
	}
	
	
	/**
	 *
	 */
	public void draw(Graphics2D grx, JRPrintRectangle rectangle, int offsetX, int offsetY)
	{
		int width = rectangle.getWidth();
		int height = rectangle.getHeight();
		
		if (rectangle.getModeValue() == ModeEnum.OPAQUE)
		{
			grx.setColor(rectangle.getBackcolor());
			if (rectangle.getRadius() > 0)
			{
				grx.fillRoundRect(
					rectangle.getX() + offsetX, 
					rectangle.getY() + offsetY, 
					width,
					height,
					2 * rectangle.getRadius(),
					2 * rectangle.getRadius()
					);
			}
			else
			{
				grx.fillRect(
					rectangle.getX() + offsetX, 
					rectangle.getY() + offsetY, 
					width,
					height
					);
			}
		}

		grx.setColor(rectangle.getLinePen().getLineColor());

		Stroke stroke = JRPenUtil.getStroke(rectangle.getLinePen(), BasicStroke.CAP_SQUARE);

		if (stroke != null && width > 0 && height > 0)
		{
			grx.setStroke(stroke);
			
			AffineTransform oldTx = grx.getTransform();

			if (rectangle.getLinePen().getLineStyleValue() == LineStyleEnum.DOUBLE)
			{
				float lineWidth = rectangle.getLinePen().getLineWidth().floatValue();
				
				if (rectangle.getRadius() > 0)
				{
					grx.translate(
						rectangle.getX() + offsetX - lineWidth / 3, 
						rectangle.getY() + offsetY - lineWidth / 3
						);
					grx.scale(
						(width + 2 * lineWidth / 3) 
							/ width, 
						(height + 2 * lineWidth / 3) 
							/ height 
						);
					grx.drawRoundRect(
						0, 
						0, 
						width,
						height,
						2 * rectangle.getRadius(),
						2 * rectangle.getRadius()
						);
					grx.setTransform(oldTx);
					grx.translate(
						rectangle.getX() + offsetX + lineWidth / 3, 
						rectangle.getY() + offsetY + lineWidth / 3
						);
					if (width > 2 * lineWidth / 3)
					{
						grx.scale(
							(width - 2 * lineWidth / 3) 
								/ width, 
							1 
							);
					}
					if (height > 2 * lineWidth / 3)
					{
						grx.scale(
							1, 
							(height - 2 * lineWidth / 3) 
								/ height 
							);
					}
					grx.drawRoundRect(
						0, 
						0, 
						width,
						height,
						2 * rectangle.getRadius(),
						2 * rectangle.getRadius()
						);
				}
				else
				{
					grx.translate(
						rectangle.getX() + offsetX - lineWidth / 3, 
						rectangle.getY() + offsetY - lineWidth / 3
						);
					grx.scale(
						(width + 2 * lineWidth / 3) 
							/ width, 
						(height + 2 * lineWidth / 3) 
							/ height 
						);
					grx.drawRect(
						0, 
						0, 
						width,
						height
						);
					grx.setTransform(oldTx);
					grx.translate(
						rectangle.getX() + offsetX + lineWidth / 3, 
						rectangle.getY() + offsetY + lineWidth / 3
						);
					if (width > 2 * lineWidth / 3)
					{
						grx.scale(
							(width - 2 * lineWidth / 3) 
								/ width, 
							1 
							);
					}
					if (height > 2 * lineWidth / 3)
					{
						grx.scale(
							1, 
							(height - 2 * lineWidth / 3) 
								/ height 
							);
					}
					grx.drawRect(
						0, 
						0, 
						width,
						height
						);
				}
			}
			else
			{
				float lineOffset = BorderOffset.getOffset(rectangle.getLinePen());
				
				grx.translate(
					rectangle.getX() + offsetX + lineOffset, 
					rectangle.getY() + offsetY + lineOffset
					);
				if (width > 2 * lineOffset)
				{
					grx.scale(
						(width - 2 * lineOffset) 
							/ width, 
						1 
						);
				}
				if (height > 2 * lineOffset)
				{
					grx.scale(
						1, 
						(height - 2 * lineOffset) 
							/ height 
						);
				}

				if (rectangle.getRadius() > 0)
				{
					grx.drawRoundRect(
						0, 
						0, 
						width,
						height,
						2 * rectangle.getRadius(),
						2 * rectangle.getRadius()
						);
				}
				else
				{
					grx.drawRect(
						0, 
						0, 
						width,
						height
						);
				}
			}

			grx.setTransform(oldTx);
		}
	}
	
	
}
