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
package net.sf.jasperreports.components.barbecue;

import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.type.RotationEnum;
import net.sf.jasperreports.engine.util.JRStyleResolver;

/**
 * 
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: BarbecueStyleResolver.java 5180 2012-03-29 13:23:12Z teodord $
 */
public final class BarbecueStyleResolver {
	
	private BarbecueStyleResolver() {
	}
	
	/**
	 * 
	 */
	public static RotationEnum getRotationValue(JRComponentElement element)	{
		RotationEnum ownRotation = ((BarbecueComponent)element.getComponent()).getOwnRotation();
		if (ownRotation != null) {
			return ownRotation;
		}
		JRStyle style = JRStyleResolver.getBaseStyle(element);
		if (style != null) {
			RotationEnum rotation = style.getRotationValue();
			if (rotation != null) {
				return rotation;
			}
		}
		return RotationEnum.NONE;
	}

}
