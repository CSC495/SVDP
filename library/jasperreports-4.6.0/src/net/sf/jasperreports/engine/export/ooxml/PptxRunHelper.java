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
package net.sf.jasperreports.engine.export.ooxml;

import java.awt.Color;
import java.awt.font.TextAttribute;
import java.io.Writer;
import java.text.AttributedCharacterIterator.Attribute;
import java.util.HashMap;
import java.util.Locale;
import java.util.Map;
import java.util.StringTokenizer;

import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.base.JRBasePrintText;
import net.sf.jasperreports.engine.fonts.FontFamily;
import net.sf.jasperreports.engine.fonts.FontInfo;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.util.JRColorUtil;
import net.sf.jasperreports.engine.util.JRFontUtil;
import net.sf.jasperreports.engine.util.JRStringUtil;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: PptxRunHelper.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class PptxRunHelper extends BaseHelper
{
	/**
	 *
	 */
	private Map<String,String> fontMap;
	private String exporterKey;


	/**
	 *
	 */
	public PptxRunHelper(Writer writer, Map<String,String> fontMap, String exporterKey)
	{
		super(writer);
		this.fontMap = fontMap;
		this.exporterKey = exporterKey;
	}


	/**
	 *
	 */
	public void export(JRStyle style, Map<Attribute,Object> attributes, String text, Locale locale)
	{
		if (text != null)
		{
			StringTokenizer tkzer = new StringTokenizer(text, "\n", true);
			while(tkzer.hasMoreTokens())
			{
				String token = tkzer.nextToken();
				if ("\n".equals(token))
				{
					write("<a:br/>");
				}
				else
				{
					write("      <a:r>\n");
					exportProps("a:rPr", getAttributes(style), attributes, locale);
					//write("<a:t xml:space=\"preserve\">");
					write("<a:t>");
					write(JRStringUtil.xmlEncode(token));//FIXMEODT try something nicer for replace
					write("</a:t>\n");
					write("      </a:r>\n");
				}
			}
		}
	}

	/**
	 *
	 */
	public void exportProps(JRStyle style, Locale locale)
	{
		JRPrintText text = new JRBasePrintText(null);
		text.setStyle(style);
		Map<Attribute,Object> styledTextAttributes = new HashMap<Attribute,Object>(); //FIXMEPPTX is this map useless; check all run helpers
		JRFontUtil.getAttributesWithoutAwtFont(styledTextAttributes, text);
		styledTextAttributes.put(TextAttribute.FOREGROUND, text.getForecolor());
		if (style.getModeValue() == null || style.getModeValue() == ModeEnum.OPAQUE)
		{
			styledTextAttributes.put(TextAttribute.BACKGROUND, style.getBackcolor());
		}

		exportProps("a:rPr", getAttributes(style.getStyle()), getAttributes(style), locale);
	}

	/**
	 *
	 */
	public void exportProps(JRPrintText text, Locale locale)
	{
		Map<Attribute,Object> textAttributes = new HashMap<Attribute,Object>(); 
		JRFontUtil.getAttributesWithoutAwtFont(textAttributes, text);
		textAttributes.put(TextAttribute.FOREGROUND, text.getForecolor());
		if (text.getModeValue() == null || text.getModeValue() == ModeEnum.OPAQUE)
		{
			textAttributes.put(TextAttribute.BACKGROUND, text.getBackcolor());
		}

		exportProps("a:defRPr", new HashMap<Attribute,Object>(), textAttributes, locale);
	}

	/**
	 *
	 */
	private void exportProps(String tag, Map<Attribute,Object> parentAttrs,  Map<Attribute,Object> attrs, Locale locale)
	{
		write("       <" + tag + "\n");

		Object value = attrs.get(TextAttribute.SIZE);
		Object oldValue = parentAttrs.get(TextAttribute.SIZE);

		if (value != null && !value.equals(oldValue))
		{
			float fontSize = ((Float)value).floatValue();
			fontSize = fontSize == 0 ? 0.5f : fontSize;// only the special EMPTY_CELL_STYLE would have font size zero
			write(" sz=\"" + (int)(100 * fontSize) + "\"");
		}
		else //FIXMEPPTX deal with default values from a style, a theme or something
		{
			float fontSize = ((Float)oldValue).floatValue();
			write(" sz=\"" + (int)(100 * fontSize) + "\"");
		}
		
		value = attrs.get(TextAttribute.WEIGHT);
		oldValue = parentAttrs.get(TextAttribute.WEIGHT);

		if (value != null && !value.equals(oldValue))
		{
			write(" b=\"" + (value.equals(TextAttribute.WEIGHT_BOLD) ? 1 : 0) + "\"");
		}

		value = attrs.get(TextAttribute.POSTURE);
		oldValue = parentAttrs.get(TextAttribute.POSTURE);

		if (value != null && !value.equals(oldValue))
		{
			write(" i=\"" + (value.equals(TextAttribute.POSTURE_OBLIQUE) ? 1 : 0) + "\"");
		}


		value = attrs.get(TextAttribute.UNDERLINE);
		oldValue = parentAttrs.get(TextAttribute.UNDERLINE);

		if (
			(value == null && oldValue != null)
			|| (value != null && !value.equals(oldValue))
			)
		{
			write(" u=\"" + (value == null ? "none" : "sng") + "\"");
		}
		
		value = attrs.get(TextAttribute.STRIKETHROUGH);
		oldValue = parentAttrs.get(TextAttribute.STRIKETHROUGH);

		if (
			(value == null && oldValue != null)
			|| (value != null && !value.equals(oldValue))
			)
		{
			write(" strike=\"" + (value == null ? "noStrike" : "sngStrike") + "\"");
		}

		value = attrs.get(TextAttribute.SUPERSCRIPT);

//		if (TextAttribute.SUPERSCRIPT_SUPER.equals(value))
//		{
//			write("        <a:vertAlign a:val=\"superscript\" />\n");
//		}
//		else if (TextAttribute.SUPERSCRIPT_SUB.equals(value))
//		{
//			write("        <a:vertAlign a:val=\"subscript\" />\n");
//		}

		write(">\n");

		value = attrs.get(TextAttribute.FOREGROUND);
		oldValue = parentAttrs.get(TextAttribute.FOREGROUND);
		
		if (value != null && !value.equals(oldValue))
		{
			write("<a:solidFill><a:srgbClr val=\"" + JRColorUtil.getColorHexa((Color)value) + "\"/></a:solidFill>\n");
		}

		value = attrs.get(TextAttribute.BACKGROUND);
		oldValue = parentAttrs.get(TextAttribute.BACKGROUND);
		
//		if (value != null && !value.equals(oldValue))
//		{
//			write("<a:solidFill><a:srgbClr val=\"" + JRColorUtil.getColorHexa((Color)value) + "\"/></a:solidFill>\n");
//		}

		value = attrs.get(TextAttribute.FAMILY);
		oldValue = parentAttrs.get(TextAttribute.FAMILY);
		
		if (value != null && !value.equals(oldValue))//FIXMEDOCX the text locale might be different from the report locale, resulting in different export font
		{
			String fontFamilyAttr = (String)value;
			String fontFamily = fontFamilyAttr;
			if (fontMap != null && fontMap.containsKey(fontFamilyAttr))
			{
				fontFamily = fontMap.get(fontFamilyAttr);
			}
			else
			{
				FontInfo fontInfo = JRFontUtil.getFontInfo(fontFamilyAttr, locale);
				if (fontInfo != null)
				{
					//fontName found in font extensions
					FontFamily family = fontInfo.getFontFamily();
					String exportFont = family.getExportFont(exporterKey);
					if (exportFont != null)
					{
						fontFamily = exportFont;
					}
				}
			}
			write("        <a:latin typeface=\"" + fontFamily + "\"/>\n");
			write("        <a:ea typeface=\"" + fontFamily + "\"/>\n");
			write("        <a:cs typeface=\"" + fontFamily + "\"/>\n");
		}
		
		write("</" + tag + ">\n");
	}


	/**
	 *
	 */
	private Map<Attribute,Object> getAttributes(JRStyle style)//FIXMEDOCX put this in util?
	{
		JRPrintText text = new JRBasePrintText(null);
		text.setStyle(style);
		
		Map<Attribute,Object> styledTextAttributes = new HashMap<Attribute,Object>(); 
		//JRFontUtil.getAttributes(styledTextAttributes, text, (Locale)null);//FIXMEDOCX getLocale());
		JRFontUtil.getAttributesWithoutAwtFont(styledTextAttributes, text);
		styledTextAttributes.put(TextAttribute.FOREGROUND, text.getForecolor());
		if (text.getModeValue() == ModeEnum.OPAQUE)
		{
			styledTextAttributes.put(TextAttribute.BACKGROUND, text.getBackcolor());
		}

		return styledTextAttributes;
	}

}

