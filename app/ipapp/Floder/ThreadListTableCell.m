//
//  PostListTableCell.m
//  ipapp
//
//  Created by kelaocai on 13-9-29.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "ThreadListTableCell.h"

@implementation PostListTableCell

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        self.contentView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"tmall_bg_main"]];
        self.logo = [[[UIImageView alloc] initWithFrame:CGRectMake(5, 5, 70, 70)] autorelease];
        self.logo.backgroundColor = [UIColor clearColor];
        [self.contentView addSubview:self.logo];
        
        self.title = [[[UILabel alloc] initWithFrame:CGRectMake(80, 10, 230, 20)] autorelease];
        self.title.font = [UIFont systemFontOfSize:16.0f];
        self.title.backgroundColor = [UIColor clearColor];
        self.title.opaque = NO;
        [self.contentView addSubview:self.title];
        
        self.subTtile = [[[UILabel alloc] initWithFrame:CGRectMake(80, 40, 80, 16)] autorelease];
        self.subTtile.font = [UIFont systemFontOfSize:16.0f];
        self.subTtile.textColor = [UIColor colorWithRed:158/255.0
                                                  green:158/255.0
                                                   blue:158/255.0
                                                  alpha:1.0];
        self.subTtile.backgroundColor = [UIColor clearColor];
        self.subTtile.opaque = NO;
        [self.contentView addSubview:self.subTtile];
      
//        UIImageView *icon_clock_view = [[[UIImageView alloc] initWithFrame:CGRectMake(170, 40, 16, 16)] autorelease];
//        icon_clock_view.image=[UIImage imageNamed:@"icon_clock.png"];
//        [self.contentView addSubview:icon_clock_view];
        
        self.time = [[[UILabel alloc] initWithFrame:CGRectMake(196, 40, 60, 16)] autorelease];
        self.time.font = [UIFont systemFontOfSize:11.0f];
        self.time.textColor = [UIColor colorWithRed:158/255.0
                                                  green:158/255.0
                                                   blue:158/255.0
                                                  alpha:1.0];
        self.time.backgroundColor = [UIColor clearColor];
        self.time.opaque = NO;
        [self.contentView addSubview:self.time];

        
        UIImageView *icon_comment_view = [[[UIImageView alloc] initWithFrame:CGRectMake(256, 40, 16, 16)] autorelease];
        icon_comment_view.image=[UIImage imageNamed:@"icon_comment.png"];
        [self.contentView addSubview:icon_comment_view];
        
        self.comment = [[[UILabel alloc] initWithFrame:CGRectMake(274, 40, 60, 16)] autorelease];
        self.comment.font = [UIFont systemFontOfSize:11.0f];
        self.comment.textColor = [UIColor colorWithRed:158/255.0
                                              green:158/255.0
                                               blue:158/255.0
                                              alpha:1.0];
        self.comment.backgroundColor = [UIColor clearColor];
        self.comment.opaque = NO;
        [self.contentView addSubview:self.comment];



        
        UILabel *sLine1 = [[[UILabel alloc] initWithFrame:CGRectMake(0, 78, 320, 1)] autorelease];
        sLine1.backgroundColor = [UIColor colorWithRed:198/255.0
                                                 green:198/255.0
                                                  blue:198/255.0
                                                 alpha:1.0];
        UILabel *sLine2 = [[[UILabel alloc] initWithFrame:CGRectMake(0, 79, 320, 1)] autorelease];
        sLine2.backgroundColor = [UIColor whiteColor];
        
        [self.contentView addSubview:sLine1];
        [self.contentView addSubview:sLine2];
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
