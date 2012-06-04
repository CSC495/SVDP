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
package net.sf.jasperreports.components.barbecue;

import net.sf.jasperreports.engine.JRCloneable;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.component.ContextAwareComponent;
import net.sf.jasperreports.engine.type.EvaluationTimeEnum;
import net.sf.jasperreports.engine.type.RotationEnum;

/**
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: BarbecueComponent.java 4595 2011-09-08 15:55:10Z teodord $
 */
public interface BarbecueComponent extends ContextAwareComponent, JRCloneable
{
	//TODO scale type, alignment

	String getType();

	JRExpression getApplicationIdentifierExpression();
	
	//TODO use Object?
	JRExpression getCodeExpression();

	boolean isDrawText();

	boolean isChecksumRequired();
	
	Integer getBarWidth();
	
	Integer getBarHeight();
	
	EvaluationTimeEnum getEvaluationTimeValue();
	
	String getEvaluationGroup();
	
	RotationEnum getRotation();
	
	RotationEnum getOwnRotation();
	
}
