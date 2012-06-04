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
package net.sf.jasperreports.engine.fonts;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import net.sf.jasperreports.engine.JRPropertiesMap;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRPropertiesUtil.PropertySuffix;
import net.sf.jasperreports.extensions.DefaultExtensionsRegistry;
import net.sf.jasperreports.extensions.ExtensionsRegistry;
import net.sf.jasperreports.extensions.ExtensionsRegistryFactory;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: SimpleFontExtensionsRegistryFactory.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class SimpleFontExtensionsRegistryFactory implements ExtensionsRegistryFactory
{

	/**
	 * 
	 */
	public final static String SIMPLE_FONT_FAMILIES_PROPERTY_PREFIX = 
		DefaultExtensionsRegistry.PROPERTY_REGISTRY_PREFIX + "simple.font.families.";
	public final static String PROPERTY_SIMPLE_FONT_FAMILIES_REGISTRY_FACTORY =
		DefaultExtensionsRegistry.PROPERTY_REGISTRY_FACTORY_PREFIX + "simple.font.families";
	
	/**
	 * 
	 */
	public ExtensionsRegistry createRegistry(String registryId, JRPropertiesMap properties)
	{
		List<PropertySuffix> fontFamiliesProperties = JRPropertiesUtil.getProperties(properties, SIMPLE_FONT_FAMILIES_PROPERTY_PREFIX);
		List<String> fontFamiliesLocations = new ArrayList<String>();
		for (Iterator<PropertySuffix> it = fontFamiliesProperties.iterator(); it.hasNext();)
		{
			PropertySuffix fontFamiliesProp = it.next();
			//String fontFamiliesName = fontFamiliesProp.getSuffix();
			String fontFamiliesLocation = fontFamiliesProp.getValue();
			//fontFamiliesLocations.addAll(SimpleFontExtensionHelper.getInstance().loadFontFamilies(fontFamiliesLocation));
			fontFamiliesLocations.add(fontFamiliesLocation);
		}
		
		return new FontExtensionsRegistry(fontFamiliesLocations);
	}

}
