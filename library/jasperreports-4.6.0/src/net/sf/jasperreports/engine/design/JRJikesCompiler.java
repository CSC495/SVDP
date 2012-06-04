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
package net.sf.jasperreports.engine.design;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.InputStream;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JasperReportsContext;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRJikesCompiler.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRJikesCompiler extends JRAbstractMultiClassCompiler
{
	/**
	 * 
	 */
	public JRJikesCompiler(JasperReportsContext jasperReportsContext)
	{
		super(jasperReportsContext);
	}

	/**
	 * @deprecated Replaced by {@link #JRJikesCompiler(JasperReportsContext)}.
	 */
	public JRJikesCompiler()
	{
		this(DefaultJasperReportsContext.getInstance());
	}

	/**
	 *
	 */
	public String compileClasses(File[] sourceFiles, String classpath) throws JRException 
	{
		String[] source = new String[sourceFiles.length + 3];
		source[0] = "jikes";
		source[1] = "-classpath";
		source[2] = classpath;
		for (int i = 0; i < sourceFiles.length; i++)
		{
			source[i + 3] = sourceFiles[i].getPath();
		}
		
		try 
		{
			// Compile the source file and arrange to read the errors if any.
			Process compile = Runtime.getRuntime().exec(source);
			InputStream errFile = compile.getErrorStream();
			
			// Read the error messages (if any) into the ByteArrayOutputStream
			ByteArrayOutputStream baos = new ByteArrayOutputStream();
			byte[] buffer = new byte[1024];
			int count = 0;
			do 
			{
				count = errFile.read(buffer);
				if (count > 0)
				{
					baos.write(buffer, 0, count);
				}
			} while (count >= 0);
			
			if( baos.toString().indexOf("error") != -1 ) 
			{
				return baos.toString();
			}
			
			return null;
		}
		catch (Exception e) 
		{
			StringBuffer files = new StringBuffer();
			for (int i = 0; i < sourceFiles.length; ++i)
			{
				files.append(sourceFiles[i].getPath());
				files.append(' ');
			}
			throw new JRException("Error compiling report java source files : " + files, e);
		}
	}


}
