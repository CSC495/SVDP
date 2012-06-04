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

import net.sf.jasperreports.engine.JRPropertiesUtil;


/**
 * Report data cache handler.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: DataCacheHandler.java 5340 2012-05-04 10:41:48Z lucianc $
 */
public interface DataCacheHandler
{
	
	String PARAMETER_DATA_CACHE_HANDLER = "net.sf.jasperreports.data.cache.handler";
	
	String PROPERTY_DATA_RECORDABLE = JRPropertiesUtil.PROPERTY_PREFIX + "data.cache.recordable";
	
	String PROPERTY_DATA_PERSISTABLE = JRPropertiesUtil.PROPERTY_PREFIX + "data.cache.persistable";
	
	String PROPERTY_INCLUDED = JRPropertiesUtil.PROPERTY_PREFIX + "data.cache.included";

	boolean isRecordingEnabled();
	
	DataRecorder createDataRecorder();
	
	boolean isSnapshotPopulated();
	
	DataSnapshot getDataSnapshot();
	
}
