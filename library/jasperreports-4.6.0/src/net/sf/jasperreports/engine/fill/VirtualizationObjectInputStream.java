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
package net.sf.jasperreports.engine.fill;

import java.io.IOException;
import java.io.InputStream;
import java.io.ObjectInputStream;

/**
 * <code>java.io.ObjectInputStream</code> subclass used for deserializing report
 * data on virtualization.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: VirtualizationObjectInputStream.java 4855 2011-12-13 14:03:08Z lucianc $
 */
public class VirtualizationObjectInputStream extends ObjectInputStream
{
	private final JRVirtualizationContext virtualizationContext;

	public VirtualizationObjectInputStream(InputStream in, 
			JRVirtualizationContext virtualizationContext) throws IOException
	{
		super(in);
		
		this.virtualizationContext = virtualizationContext;
		enableResolveObject(true);
	}

	@Override
	protected Object resolveObject(Object obj) throws IOException
	{
		return virtualizationContext.resolveSerializedObject(obj);
	}
}
