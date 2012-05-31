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
package net.sf.jasperreports.engine.util.xml;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import net.sf.jasperreports.engine.JRException;

import org.apache.commons.collections.ReferenceMap;
import org.jaxen.JaxenException;
import org.jaxen.NamespaceContext;
import org.jaxen.XPath;
import org.jaxen.dom.DOMXPath;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;


/**
 * XPath executer implementation that uses a namespace aware <a href="http://jaxen.org/" target="_blank">Jaxen</a>.
 * 
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JaxenNsAwareXPathExecuter.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JaxenNsAwareXPathExecuter extends JaxenXPathExecuter
{

	private final Map<String,XPath> cachedXPaths = new ReferenceMap();//soft cache
	
	private Map<String, String> xmlNamespaceMap;
	
	private NamespaceContext context;
	
	boolean detectXmlNamespaces;
	
	/**
	 * Default constructor.
	 */
	public JaxenNsAwareXPathExecuter()
	{
	}
	

	public Map<String, String> getXmlNamespaceMap() 
	{
		return xmlNamespaceMap;
	}
	
	
	public void setXmlNamespaceMap(Map<String, String> xmlNamespaceMap) throws JRException 
	{
		this.xmlNamespaceMap = xmlNamespaceMap;
	}
	
	
	public boolean getDetectXmlNamespaces()
	{
		return detectXmlNamespaces;
	}


	public void setDetectXmlNamespaces(boolean detectXmlNamespaces) 
	{
		this.detectXmlNamespaces = detectXmlNamespaces;
	}
	
	protected XPath getXPath(Node contextNode, String expression) throws JRException
	{
		XPath xPath = cachedXPaths.get(expression);
		if (xPath == null)
		{
			try
			{
				xPath = new DOMXPath(expression);
				addNamespaceContext(contextNode, xPath, expression);
			}
			catch (JaxenException e)
			{
				throw new JRException("XPath compilation failed. Expression: " + expression, e);
			}
			cachedXPaths.put(expression, xPath);
		}
		return xPath;
	}
	
	
	public NodeList selectNodeList(Node contextNode, String expression) throws JRException
	{
		try
		{
			XPath xpath = getXPath(contextNode, expression);
			Object object = xpath.evaluate(contextNode);
			List<Object> nodes;
			if (object instanceof List<?>)
			{
				nodes = (List<Object>) object;
			}
			else
			{
				nodes = new ArrayList<Object>();
				nodes.add(object);
			}
			return new NodeListWrapper(nodes);
		}
		catch (JaxenException e)
		{
			throw new JRException("XPath selection failed. Expression: " + expression, e);
		}		
	}

	public Object selectObject(Node contextNode, String expression) throws JRException
	{
		try
		{
			XPath xpath = getXPath(contextNode, expression);
			Object object = xpath.evaluate(contextNode);
			Object value;
			if (object instanceof List<?>)
			{
				List<?> list = (List<?>) object;
				if (list.isEmpty())
				{
					value = null;
				}
				else
				{
					value = list.get(0);
				}
			}
			else if (object instanceof Number || object instanceof Boolean)
			{
				value = object;
			}
			else
			{
				value = object.toString();
			}
			return value;
		}
		catch (JaxenException e)
		{
			throw new JRException("XPath selection failed. Expression: " + expression, e);
		}
	}
	
	protected boolean containsPrefixes(String expression) {
		String[] tokens = expression.split("::");
		for (String token: tokens) 
		{
			if (token.indexOf(":") != -1) 
			{
				return true;
			}
		}
		return false;
	}
	
	private Map<String, String> extractXmlNamespaces(Node contextNode) throws JRException 
	{
		Map<String, String> namespaces = new HashMap<String, String>();
		List<Node> nlist;
		String namespaceXPathString = "//namespace::node()";

		try
		{
			XPath xpath = new DOMXPath(namespaceXPathString);
			nlist = xpath.selectNodes(contextNode);
			
			for (int i = 0; i < nlist.size(); i++) 
			{
				Node node = nlist.get(i);
				if(node.getParentNode() != null && node.getParentNode().getPrefix() != null)
				{
					if (!namespaces.containsKey(node.getParentNode().getPrefix()))
					{
						namespaces.put(node.getParentNode().getPrefix(), node.getParentNode().getNamespaceURI());
					}
				}
			}
			
		} catch (JaxenException e)
		{
			throw new JRException("XPath selection failed. Expression: " + namespaceXPathString, e);
		}
		
		return namespaces;
	}
	
	protected void addNamespaceContext(Node contextNode, XPath xPath, String expression) throws JRException {
		if (xmlNamespaceMap == null && detectXmlNamespaces && containsPrefixes(expression) && context == null)
		{
			xmlNamespaceMap = extractXmlNamespaces(contextNode);
		}
		
		if (xmlNamespaceMap != null && xmlNamespaceMap.size() > 0)
		{
			if (context == null) {
				context = new NamespaceContext() {
					
					public String translateNamespacePrefixToUri(String prefix) {
						return xmlNamespaceMap.get(prefix);
					}
				};
			}
			xPath.setNamespaceContext(context);
		}
	}
	
}
