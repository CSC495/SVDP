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
package net.sf.jasperreports.engine.convert;

import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JRElement;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.component.ComponentKey;
import net.sf.jasperreports.engine.component.ComponentManager;
import net.sf.jasperreports.engine.component.ComponentsEnvironment;
import net.sf.jasperreports.engine.util.JRImageLoader;


/**
 * Converter of {@link JRComponentElement} into print elements.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: ComponentElementConverter.java 5050 2012-03-12 10:11:26Z teodord $
 */
public final class ComponentElementConverter extends ElementConverter
{
	
	private final static ComponentElementConverter INSTANCE = new ComponentElementConverter();

	/**
	 * Returns the singleton instance of this converter.
	 * 
	 * @return the singleton component converter instance 
	 */
	public static ComponentElementConverter getInstance()
	{
		return INSTANCE;
	}
	
	private final static ElementIconConverter ICON_CONVERTER = new ElementIconConverter(
			JRImageLoader.COMPONENT_IMAGE_RESOURCE);
	
	private ComponentElementConverter()
	{
	}

	public JRPrintElement convert(ReportConverter reportConverter,
			JRElement element)
	{
		JRComponentElement componentElement = (JRComponentElement) element;
		JRPrintElement converted = null;
		ComponentKey componentKey = componentElement.getComponentKey();
		if (componentKey != null)
		{
			ComponentManager manager = ComponentsEnvironment.getInstance(reportConverter.getJasperReportsContext()).getManager(
					componentKey);
			if (manager != null && manager.getDesignConverter() != null)
			{
				// convert using the component converter
				converted = manager.getDesignConverter().convert(reportConverter, 
						componentElement);
			}
		}
		
		if (converted == null)
		{
			// fallback to the icon converter
			converted = ICON_CONVERTER.convert(reportConverter, element);
		}
		
		return converted;
	}

}
