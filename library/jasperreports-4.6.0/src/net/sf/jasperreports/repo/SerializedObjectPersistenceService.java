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

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.util.JRLoader;
import net.sf.jasperreports.engine.util.JRSaver;





/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: SerializedObjectPersistenceService.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class SerializedObjectPersistenceService implements PersistenceService
{

	/**
	 * 
	 */
	public Resource load(String uri, RepositoryService repositoryService)
	{
		ObjectResource resource = null; 

		InputStreamResource isResource = repositoryService.getResource(uri, InputStreamResource.class);
		
		InputStream is = isResource == null ? null : isResource.getInputStream();
		if (is != null)
		{
			resource = new ObjectResource();
			try
			{
				resource.setValue(JRLoader.loadObject(is));
			}
			catch (JRException e)
			{
				throw new JRRuntimeException(e);
			}
			finally
			{
				try
				{
					is.close();
				}
				catch (IOException e)
				{
				}
			}
		}

		return resource;
	}
	
	/**
	 * 
	 */
	public void save(Resource resource, String uri, RepositoryService repositoryService)
	{
		ObjectResource objectResource = (ObjectResource)resource;
		
		OutputStreamResource osResource = repositoryService.getResource(uri, OutputStreamResource.class);
		
		OutputStream os = osResource == null ? null : osResource.getOutputStream();
		if (os != null)
		{
			try
			{
				JRSaver.saveObject(objectResource.getValue(), os);
			}
			catch (JRException e)
			{
				throw new JRRuntimeException(e);
			}
			finally
			{
				try
				{
					os.close();
				}
				catch (IOException e)
				{
				}
			}
		}
		
	}
	
}
