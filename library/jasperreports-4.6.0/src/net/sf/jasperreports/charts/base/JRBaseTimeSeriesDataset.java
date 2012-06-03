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
package net.sf.jasperreports.charts.base;

import net.sf.jasperreports.charts.JRTimeSeries;
import net.sf.jasperreports.charts.JRTimeSeriesDataset;
import net.sf.jasperreports.engine.JRChartDataset;
import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.base.JRBaseChartDataset;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.design.JRVerifier;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.util.JRCloneUtils;

/**
 * @author Flavius Sana (flavius_sana@users.sourceforge.net)
 * @version $Id: JRBaseTimeSeriesDataset.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRBaseTimeSeriesDataset extends JRBaseChartDataset implements JRTimeSeriesDataset, JRChangeEventsSupport {
	
	/**
	 * 
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	public static final String PROPERTY_TIME_PERIOD = "timePeriod";
	
	private JRTimeSeries[] timeSeries;
	private Class<?> timePeriod;
	
	protected JRBaseTimeSeriesDataset( JRTimeSeriesDataset dataset ){
		super( dataset );
	}
	
	public JRBaseTimeSeriesDataset( JRTimeSeriesDataset dataset, JRBaseObjectFactory factory ){
		super( dataset, factory );
		
		timePeriod = dataset.getTimePeriod();
		JRTimeSeries[] srcTimeSeries = dataset.getSeries();
		
		if( srcTimeSeries != null && srcTimeSeries.length > 0 ){
			timeSeries = new JRTimeSeries[ srcTimeSeries.length ];
			for( int i = 0; i< timeSeries.length; i++ ){
				timeSeries[i] = factory.getTimeSeries( srcTimeSeries[i]);
			}
		}
	}
	
	public JRTimeSeries[] getSeries(){
		return timeSeries;
	}
	
	public Class<?> getTimePeriod(){
		return timePeriod;
	}
	
	public void setTimePeriod( Class<?> timePeriod ){
		Object old = this.timePeriod;
		this.timePeriod = timePeriod;
		getEventSupport().firePropertyChange(PROPERTY_TIME_PERIOD, old, this.timePeriod);
	}


	/** 
	 * 
	 */
	public byte getDatasetType() {
		return JRChartDataset.TIMESERIES_DATASET;
	}


	/**
	 *
	 */
	public void collectExpressions(JRExpressionCollector collector)
	{
		collector.collect(this);
	}


	public void validate(JRVerifier verifier)
	{
		verifier.verify(this);
	}

	/**
	 * 
	 */
	public Object clone() 
	{
		JRBaseTimeSeriesDataset clone = (JRBaseTimeSeriesDataset)super.clone();
		clone.timeSeries = JRCloneUtils.cloneArray(timeSeries);
		clone.eventSupport = null;
		return clone;
	}
	
	private transient JRPropertyChangeSupport eventSupport;
	
	public JRPropertyChangeSupport getEventSupport()
	{
		synchronized (this)
		{
			if (eventSupport == null)
			{
				eventSupport = new JRPropertyChangeSupport(this);
			}
		}
		
		return eventSupport;
	}

}
