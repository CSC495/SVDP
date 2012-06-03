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
package net.sf.jasperreports.repo;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: CastorMapping.java 5397 2012-05-21 01:10:02Z teodord $
 * @deprecated Replaced by {@link net.sf.jasperreports.util.CastorMapping}.
 */
public class CastorMapping
{
	private String path;
	
	/**
	 * 
	 */
	public CastorMapping(String path)
	{
		this.path = path;
	}

	/**
	 * 
	 */
	public String getPath()
	{
		return path;
	}

	/**
	 * 
	 */
	public void setPath(String path)
	{
		this.path = path;
	}
}
