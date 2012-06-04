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

import java.io.IOException;
import java.io.Serializable;

import net.sf.jasperreports.engine.JRConstants;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: ConstantColumnValue.java 5131 2012-03-27 09:07:10Z lucianc $
 */
public class ConstantColumnValue implements ColumnValues, Serializable
{

	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	private int size;
	private Object value;
	
	public ConstantColumnValue(int size, Object value)
	{
		this.size = size;
		this.value = value;
	}
	
	private void writeObject(java.io.ObjectOutputStream out) throws IOException
	{
		out.writeInt(size);
		out.writeObject(value);
	}
	
	private void readObject(java.io.ObjectInputStream in) throws IOException, ClassNotFoundException
	{
		size = in.readInt();
		value = in.readObject();
	}

	public int size()
	{
		return size;
	}

	public ColumnValuesIterator iterator()
	{
		return new IndexColumnValueIterator(size)
		{
			public Object get()
			{
				return value;
			}
		};
	}

}
