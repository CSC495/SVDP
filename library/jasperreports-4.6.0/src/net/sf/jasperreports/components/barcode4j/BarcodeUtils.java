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
package net.sf.jasperreports.components.barcode4j;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRPropertiesHolder;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.util.JRSingletonCache;

/**
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: BarcodeUtils.java 5050 2012-03-12 10:11:26Z teodord $
 */
public final class BarcodeUtils
{
	protected static JRSingletonCache<BarcodeImageProducer> imageProducerCache = 
		new JRSingletonCache<BarcodeImageProducer>(BarcodeImageProducer.class);

	private JasperReportsContext jasperReportsContext;


	/**
	 *
	 */
	private BarcodeUtils(JasperReportsContext jasperReportsContext)
	{
		this.jasperReportsContext = jasperReportsContext;
	}
	
	
	/**
	 *
	 */
	private static BarcodeUtils getDefaultInstance()
	{
		return new BarcodeUtils(DefaultJasperReportsContext.getInstance());
	}
	
	
	/**
	 *
	 */
	public static BarcodeUtils getInstance(JasperReportsContext jasperReportsContext)
	{
		return new BarcodeUtils(jasperReportsContext);
	}
	
	
	public BarcodeImageProducer getProducer(JRPropertiesHolder propertiesHolder)
	{
		String producerProperty = JRPropertiesUtil.getInstance(jasperReportsContext).getProperty(propertiesHolder, 
				BarcodeImageProducer.PROPERTY_IMAGE_PRODUCER);
		
		String producerClass = JRPropertiesUtil.getInstance(jasperReportsContext).getProperty(propertiesHolder, 
				BarcodeImageProducer.PROPERTY_PREFIX_IMAGE_PRODUCER + producerProperty);
		if (producerClass == null)
		{
			producerClass = producerProperty;
		}
		
		try
		{
			return imageProducerCache.getCachedInstance(producerClass);
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}

	/**
	 * @deprecated Replaced by {@link #getProducer(JRPropertiesHolder)}.
	 */
	public static BarcodeImageProducer getImageProducer(JRPropertiesHolder propertiesHolder)
	{
		return getDefaultInstance().getProducer(propertiesHolder);
	}

	public static boolean isVertical(BarcodeComponent barcode)
	{
		int orientation = barcode.getOrientation();
		return orientation == BarcodeComponent.ORIENTATION_LEFT
				|| orientation == BarcodeComponent.ORIENTATION_RIGHT;
	}
}
