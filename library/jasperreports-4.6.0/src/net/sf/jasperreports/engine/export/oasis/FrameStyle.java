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
package net.sf.jasperreports.engine.export.oasis;

import java.io.IOException;
import java.io.Writer;

import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.util.JRColorUtil;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: FrameStyle.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class FrameStyle extends BorderStyle
{
	/**
	 *
	 */
	private String fill;
	private String backcolor;

	/**
	 *
	 */
	public FrameStyle(Writer styleWriter, JRPrintElement element)
	{
		super(styleWriter);
		
		if (element.getModeValue() == ModeEnum.OPAQUE)
		{
			fill = "solid";
			backcolor = JRColorUtil.getColorHexa(element.getBackcolor());
		}
		else
		{
			fill = "none";
		}
	}
	
	/**
	 *
	 */
	public String getId()
	{
		return fill + "|" + backcolor + "|" + super.getId(); 
	}

	/**
	 *
	 */
	public void write(String frameStyleName) throws IOException
	{
		styleWriter.write("<style:style style:name=\"");
		styleWriter.write(frameStyleName);
		styleWriter.write("\" style:family=\"graphic\"");
//		styleWriter.write(" style:parent-style-name=\"Frame\"" +
		styleWriter.write(">\n");
		styleWriter.write(" <style:graphic-properties");
//			styleWriter.write(" style:run-through=\"foreground\"");
//			styleWriter.write(" style:wrap=\"run-through\"");
//			styleWriter.write(" style:number-wrapped-paragraphs=\"no-limit\"");
//			styleWriter.write(" style:wrap-contour=\"false\"");
		styleWriter.write(" style:vertical-pos=\"from-top\"");
		styleWriter.write(" style:vertical-rel=\"page\"");
		styleWriter.write(" style:horizontal-pos=\"from-left\"");
		styleWriter.write(" style:horizontal-rel=\"page\"");
		styleWriter.write(" draw:fill=\"");
		styleWriter.write(fill);
		styleWriter.write("\" draw:fill-color=\"#");
		styleWriter.write(backcolor);
		styleWriter.write("\"");

		writeBorder(TOP_BORDER);
		writeBorder(LEFT_BORDER);
		writeBorder(BOTTOM_BORDER);
		writeBorder(RIGHT_BORDER);
		
		styleWriter.write("/>\n");
		styleWriter.write("</style:style>\n");
	}

}

