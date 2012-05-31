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
package net.sf.jasperreports.engine.util;

import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRDatasetRun;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperReport;

/**
 * 
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRReportUtils.java 4595 2011-09-08 15:55:10Z teodord $
 */
public final class JRReportUtils
{
	
	public static JRDataset findSubdataset(JRDatasetRun datasetRun, 
			JasperReport report)
	{
		JRDataset[] datasets = report.getDatasets();
		JRDataset reportDataset = null;
		if (datasets != null)
		{
			for (int i = 0; i < datasets.length; i++)
			{
				if (datasetRun.getDatasetName().equals(
						datasets[i].getName()))
				{
					reportDataset = datasets[i];
					break;
				}
			}
		}
		
		if (reportDataset == null)
		{
			throw new JRRuntimeException("Could not find subdataset named \"" 
					+ datasetRun.getDatasetName() + "\" in report \"" 
					+ report.getName() + "\"");
		}
		return reportDataset;
	}


	private JRReportUtils()
	{
	}
}
