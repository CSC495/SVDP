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

import java.awt.font.TextAttribute;
import java.text.AttributedCharacterIterator.Attribute;
import java.util.HashMap;
import java.util.Map;

import javax.swing.text.AttributeSet;
import javax.swing.text.StyleConstants;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JEditorPaneMarkupProcessor.java 4595 2011-09-08 15:55:10Z teodord $
 */
public abstract class JEditorPaneMarkupProcessor implements MarkupProcessor
{

	/**
	 * 
	 */
	public static final class RtfFactory implements MarkupProcessorFactory
	{ 
		public MarkupProcessor createMarkupProcessor()
		{
			return JEditorPaneRtfMarkupProcessor.getInstance();
		}
	}

	/**
	 * 
	 */
	public static final class HtmlFactory implements MarkupProcessorFactory
	{ 
		public MarkupProcessor createMarkupProcessor()
		{
			return JEditorPaneHtmlMarkupProcessor.getInstance();
		}
	}

	/**
	 * 
	 */
	protected Map<Attribute,Object> getAttributes(AttributeSet attrSet) 
	{
		Map<Attribute,Object> attrMap = new HashMap<Attribute,Object>();
		if (attrSet.isDefined(StyleConstants.FontFamily))
		{
			attrMap.put(
				TextAttribute.FAMILY,
				StyleConstants.getFontFamily(attrSet)
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Bold))
		{
			attrMap.put(
				TextAttribute.WEIGHT,
				StyleConstants.isBold(attrSet) ? TextAttribute.WEIGHT_BOLD : TextAttribute.WEIGHT_REGULAR
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Italic))
		{
			attrMap.put(
				TextAttribute.POSTURE,
				StyleConstants.isItalic(attrSet) ? TextAttribute.POSTURE_OBLIQUE : TextAttribute.POSTURE_REGULAR
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Underline))
		{
			attrMap.put(
				TextAttribute.UNDERLINE,
				StyleConstants.isUnderline(attrSet) ? TextAttribute.UNDERLINE_ON : null
				);
		}
					
		if (attrSet.isDefined(StyleConstants.StrikeThrough))
		{
			attrMap.put(
				TextAttribute.STRIKETHROUGH,
				StyleConstants.isStrikeThrough(attrSet) ? TextAttribute.STRIKETHROUGH_ON : null
				);
		}
					
		if (attrSet.isDefined(StyleConstants.FontSize))
		{
			attrMap.put(
				TextAttribute.SIZE,
				new Float(StyleConstants.getFontSize(attrSet))
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Foreground))
		{
			attrMap.put(
				TextAttribute.FOREGROUND,
				StyleConstants.getForeground(attrSet)
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Background))
		{
			attrMap.put(
				TextAttribute.BACKGROUND,
				StyleConstants.getBackground(attrSet)
				);
		}
		
		//FIXME: why StyleConstants.isSuperscript(attrSet) does return false
		if (attrSet.isDefined(StyleConstants.Superscript) && !StyleConstants.isSubscript(attrSet))
		{
			attrMap.put(
				TextAttribute.SUPERSCRIPT,
				TextAttribute.SUPERSCRIPT_SUPER
				);
		}
					
		if (attrSet.isDefined(StyleConstants.Subscript) && StyleConstants.isSubscript(attrSet))
		{
			attrMap.put(
				TextAttribute.SUPERSCRIPT,
				TextAttribute.SUPERSCRIPT_SUB
				);
		}
					
		return attrMap;
	}

	/**
	 * 
	 */
	public abstract String convert(String srcText);
	
}
