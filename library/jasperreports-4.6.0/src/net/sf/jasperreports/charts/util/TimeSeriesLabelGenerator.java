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
package net.sf.jasperreports.charts.util;

import java.util.Map;

import net.sf.jasperreports.engine.JRConstants;

import org.jfree.chart.labels.StandardXYItemLabelGenerator;
import org.jfree.data.time.RegularTimePeriod;
import org.jfree.data.time.TimeSeriesCollection;
import org.jfree.data.xy.XYDataset;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: TimeSeriesLabelGenerator.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class TimeSeriesLabelGenerator extends StandardXYItemLabelGenerator 
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	private Map<Comparable<?>, Map<RegularTimePeriod, String>> labelsMap;
	
	public TimeSeriesLabelGenerator(Map<Comparable<?>, Map<RegularTimePeriod, String>> labelsMap)
	{
		this.labelsMap = labelsMap;
	}
	
	public String generateLabel(XYDataset dataset, int series, int item)
	{
		Comparable<?> seriesName = dataset.getSeriesKey(series);
		Map<RegularTimePeriod, String> labels = labelsMap.get(seriesName);
		if(labels != null)
		{
			return labels.get(((TimeSeriesCollection)dataset).getSeries(series).getTimePeriod(item));
		}
		return super.generateLabel( dataset, series, item );
	}
}