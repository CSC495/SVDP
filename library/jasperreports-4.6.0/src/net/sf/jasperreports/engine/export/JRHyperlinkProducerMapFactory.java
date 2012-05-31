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

import java.io.Serializable;
import java.util.HashMap;
import java.util.Map;

import net.sf.jasperreports.engine.JRConstants;


/**
 * Map-based hyperlink producer factory implementation.
 * <p>
 * This implementation wraps a hyperlink type to hyperling producer instance
 * association map.
 * </p>
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRHyperlinkProducerMapFactory.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRHyperlinkProducerMapFactory extends JRHyperlinkProducerFactory implements Serializable
{
	
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	private Map<String,JRHyperlinkProducer> producers;
	
	
	/**
	 * Creates a blank factory.
	 */
	public JRHyperlinkProducerMapFactory()
	{
		producers = new HashMap<String,JRHyperlinkProducer>();
	}

	
	/**
	 * Exposes the type to producer association map.
	 * 
	 * @return the type to producer association map
	 */
	public Map<String,JRHyperlinkProducer> getProducersMap()
	{
		return producers;
	}

	
	/**
	 * Sets the type to producer association map.
	 *
	 * @param producers bulk type to producer association map
	 * @see #getProducersMap()
	 */
	public void setProducersMap(Map<String,JRHyperlinkProducer> producers)
	{
		this.producers = producers;
	}
	
	
	/**
	 * Adds a hyperlink producer instance associated to a hyperlink type.
	 *  
	 * @param linkType the type
	 * @param producer the producer
	 */
	public void addProducer(String linkType, JRHyperlinkProducer producer)
	{
		producers.put(linkType, producer);
	}
	
	
	/**
	 * Removes a type to producer association.
	 * 
	 * @param linkType the hyperlink type
	 * @return the producer which was associated to the type, if any
	 */
	public JRHyperlinkProducer removeProducer(String linkType)
	{
		return producers.remove(linkType);
	}
	
	
	public JRHyperlinkProducer getHandler(String linkType)
	{
		return producers.get(linkType);
	}

}
