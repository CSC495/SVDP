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
 * @version $Id: PopulatedSnapshotCacheHandler.java 5231 2012-04-06 09:20:06Z lucianc $
 */
public class PopulatedSnapshotCacheHandler implements DataCacheHandler
{
	
	private final DataSnapshot snapshot;
	
	public PopulatedSnapshotCacheHandler(DataSnapshot snapshot)
	{
		this.snapshot = snapshot;
	}

	public boolean isRecordingEnabled()
	{
		return false;
	}

	public DataRecorder createDataRecorder()
	{
		throw new UnsupportedOperationException();
	}

	public boolean isSnapshotPopulated()
	{
		return true;
	}

	public DataSnapshot getDataSnapshot()
	{
		return snapshot;
	}

}
