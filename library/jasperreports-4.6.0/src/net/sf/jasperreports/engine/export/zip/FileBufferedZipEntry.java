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
package net.sf.jasperreports.engine.export.zip;

import java.io.BufferedWriter;
import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Writer;

import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.util.FileBufferedOutputStream;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: FileBufferedZipEntry.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class FileBufferedZipEntry implements ExportZipEntry 
{
	/**
	 * 
	 */
	private String name;
	private FileBufferedOutputStream fbos;
	private Writer writer;
	
	/**
	 * 
	 */
	public FileBufferedZipEntry(String name)
	{
		this(name, null);
	}
	
	/**
	 * 
	 */
	public FileBufferedZipEntry(String name, byte[] bytes)
	{
		this.name = name;

		if (bytes == null)
		{
			fbos = new FileBufferedOutputStream();
		}
		else
		{
			fbos = new FileBufferedOutputStream(bytes.length);
			try
			{
				fbos.write(bytes);
			}
			catch (IOException e)
			{
				throw new JRRuntimeException(e);
			}
		}
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
	public Writer getWriter()
	{
		if (writer == null)
		{
			try
			{
				writer = new BufferedWriter(new OutputStreamWriter(fbos, "UTF-8"));
			}
			catch (IOException e)
			{
				throw new JRRuntimeException(e);
			}
		}
		
		return writer;
	}

	/**
	 * 
	 */
	public OutputStream getOutputStream()
	{
		return fbos;
	}

	/**
	 * 
	 */
	public void writeData(OutputStream os) throws IOException
	{
		fbos.writeData(os);
	}

	/**
	 * 
	 */
	public void dispose()
	{
		fbos.dispose();
	}
	
}
