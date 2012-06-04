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
 * Greg Hilton 
 */

package net.sf.jasperreports.engine.export;

import java.util.HashMap;
import java.util.Map;


/**
 * Utility class used by grid exporters to create a grid for page layout.
 * 
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: Cut.java 5359 2012-05-09 16:07:51Z shertage $
 */
public class Cut
{
	public static final int USAGE_NOT_EMPTY = 1;
	public static final int USAGE_SPANNED = 2;
	public static final int USAGE_BREAK = 4;
	
	private int position;
	private int usage;
	
	private Map<String, Object> propertiesMap;
	
	public Cut()
	{
		propertiesMap =  new HashMap<String, Object>();
	}
	
	public int getPosition()
	{
		return position;
	}
	
	public void setPosition(int position)
	{
		this.position = position;
	}
	
	public int getUsage()
	{
		return usage;
	}
	
	public void setUsage(int usage)
	{
		this.usage = usage;
	}

	/**
	 * Decides whether this cut is empty or not.
	 * 
	 * @return <code>true</code> if and only if the cut is not empty
	 */
	public boolean isCutNotEmpty()
	{
		return ((getUsage() & Cut.USAGE_NOT_EMPTY) > 0);
	}

	/**
	 * Decides whether this cut is occupied by spanning cells or not.
	 * 
	 * @return <code>true</code> if and only if the cut is not empty
	 */
	public boolean isCutSpanned()
	{
		return ((getUsage() & Cut.USAGE_SPANNED) > 0);
	}

	public Map<String, Object> getPropertiesMap() 
	{
		return propertiesMap;
	}

}
