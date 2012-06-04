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
package net.sf.jasperreports.web.util;




/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JacksonMapping.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JacksonMapping
{
	private String name;
	private String className;
	
	/**
	 * 
	 */
	public JacksonMapping(String name, String className)
	{
		this.name = name;
		this.className = className;
	}

	/**
	 * 
	 */
	public String getName()
	{
		return name;
	}

	/**
	 * 
	 */
	public void setName(String name)
	{
		this.name = name;
	}

	/**
	 * 
	 */
	public String getClassName()
	{
		return className;
	}

	/**
	 * 
	 */
	public void setClassName(String className)
	{
		this.className = className;
	}
}
