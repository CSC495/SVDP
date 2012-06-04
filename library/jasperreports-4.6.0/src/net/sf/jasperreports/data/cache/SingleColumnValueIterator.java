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
package net.sf.jasperreports.data.cache;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: SingleColumnValueIterator.java 5082 2012-03-15 09:44:16Z lucianc $
 */
public class SingleColumnValueIterator implements ColumnValuesIterator
{

	private final Object value;
	private boolean used;

	public SingleColumnValueIterator(Object value)
	{
		this.value = value;
		this.used = false;
	}

	public void moveFirst()
	{
		this.used = false;
	}

	public boolean next()
	{
		if (used)
		{
			return false;
		}
		
		used = true;
		return true;
	}

	public Object get()
	{
		if (!used)
		{
			throw new IllegalStateException();
		}
		
		return value;
	}
	
}