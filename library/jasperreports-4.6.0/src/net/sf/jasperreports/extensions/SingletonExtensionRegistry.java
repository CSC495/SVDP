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
package net.sf.jasperreports.extensions;

import java.util.Collections;
import java.util.List;

/**
 * An extension registry that contains a single extension.
 * 
 * @param <Type> the extension type
 *  
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: SingletonExtensionRegistry.java 4600 2011-09-12 10:32:17Z teodord $
 */
public class SingletonExtensionRegistry<Type> implements ExtensionsRegistry
{

	private final Class<Type> type;
	private final List<Type> extensions;
	
	/**
	 * Creates a singleton extension registry.
	 * 
	 * @param type the registry type
	 * @param extension the extension object
	 */
	public SingletonExtensionRegistry(Class<Type> type, Type extension)
	{
		this.type = type;
		this.extensions = Collections.singletonList(extension);
	}
	
	public <T> List<T> getExtensions(Class<T> extensionType)
	{
		if (type.equals(extensionType))
		{
			return (List<T>) extensions;
		}
		return null;
	}

}
