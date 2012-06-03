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
package net.sf.jasperreports.engine.export;

import java.util.Map;

import net.sf.jasperreports.engine.JRRuntimeException;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * The default {@link GenericElementHandlerBundle} implementation.
 * 
 * <p>
 * This implementation uses a {@link Map map} to keep element handlers.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: DefaultElementHandlerBundle.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class DefaultElementHandlerBundle implements GenericElementHandlerBundle
{

	private static final Log log = LogFactory.getLog(DefaultElementHandlerBundle.class);
	
	private String namespace;
	private Map<String, Map<String,GenericElementHandler>> elementHandlers;

	/**
	 * Uses the handler map to locate a handler for the element name
	 * and exporter key.
	 * 
	 * @throws JRRuntimeException if no handler is found
	 */
	public GenericElementHandler getHandler(String elementName,
			String exporterKey)
	{
		Map<String,GenericElementHandler> handlers = elementHandlers.get(elementName);
		if (handlers == null)
		{
			throw new JRRuntimeException("No handlers for generic elements of type "
					+ namespace + "#" + elementName);
		}
		
		GenericElementHandler handler = handlers.get(exporterKey);
		
		if (handler == null && log.isDebugEnabled())
		{
			log.debug("No " + exporterKey 
					+ " handler for generic elements of type "
					+ namespace + "#" + elementName);
		}
		
		return handler;
	}

	public String getNamespace()
	{
		return namespace;
	}
	
	/**
	 * Sets the namespace of this bundle.
	 * 
	 * @param namespace the namespace
	 * @see #getNamespace()
	 */
	public void setNamespace(String namespace)
	{
		this.namespace = namespace;
	}

	/**
	 * Returns the map of element handlers.
	 * 
	 * @return the map of element handlers
	 */
	public Map<String, Map<String,GenericElementHandler>> getElementHandlers()
	{
		return elementHandlers;
	}

	/**
	 * Sets the map of element handlers.
	 * 
	 * <p>
	 * The map needs to be a two level map, the first one indexed by element
	 * names and the second level indexed by exporter keys.
	 * 
	 * @param elementHandlers the map of element handlers
	 */
	public void setElementHandlers(Map<String,Map<String,GenericElementHandler>> elementHandlers)
	{
		this.elementHandlers = elementHandlers;
	}

}
